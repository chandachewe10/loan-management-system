<?php

namespace App\Filament\Resources;

use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Filament\Forms\Components\Toggle;
use App\helpers\CreateLinks;
use Carbon\Carbon;
use Filament\Forms\Set;
use Filament\Forms\Get;
use App\Filament\Resources\LoanResource\Pages;
use App\Filament\Resources\LoanResource\RelationManagers;
use Bavix\Wallet\Models\Wallet;
use Illuminate\Support\Facades\Storage;
use App\Models\Loan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Exports\LoanExporter;
use Filament\Tables\Actions\ExportAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class LoanResource extends Resource
{
    protected static ?string $model = Loan::class;

    protected static ?string $navigationGroup = 'Loans';
    protected static ?string $navigationIcon = 'fas-dollar-sign';
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationItems(): array
    {
        $items = parent::getNavigationItems();
        
        // Exclude cash flow statement from making Loans navigation active
        $excludedPaths = [
            'admin/loans/cash-flow-statement',
        ];
        
        $currentPath = request()->path();
        
        if (in_array($currentPath, $excludedPaths)) {
            // Create new navigation items with custom active check that always returns false
            return array_map(function ($item) {
                // Clone the item and override isActiveWhen to return false
                $newItem = \Filament\Navigation\NavigationItem::make($item->getLabel())
                    ->url($item->getUrl())
                    ->icon($item->getIcon())
                    ->group($item->getGroup())
                    ->sort($item->getSort())
                    ->isActiveWhen(fn (): bool => false);
                
                // Add badge if it exists
                if ($badge = $item->getBadge()) {
                    $newItem->badge($badge);
                }
                
                return $newItem;
            }, $items);
        }
        
        return $items;
    }



    public static function form(Form $form): Form
    {
        $options = Wallet::where('organization_id', "=", auth()->user()->organization_id)->where('branch_id', "=", auth()->user()->branch_id)->get()->map(function ($wallet) {
            return [
                'value' => $wallet->id,
                'label' => $wallet->name . ' - Balance: ' . number_format($wallet->balance)
            ];
        });


        return $form
            ->schema([
                Forms\Components\Select::make('loan_type_id')
                    ->prefixIcon('heroicon-o-wallet')
                    ->relationship('loan_type', 'loan_name')
                    ->searchable()
                    ->required()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                        if ($state) {
                            $interest_cycle = \App\Models\LoanType::findOrFail($state)->first();
                            $set('duration_period', $interest_cycle->interest_cycle);

                            $service_fee = 0.00;
                            $service_fee_data = \App\Models\LoanType::findOrFail($state);

                            if ($service_fee_data->service_fee_type === 'service_fee_percentage') {
                                $service_fee = ($get('principal_amount') * $service_fee_data->service_fee_percentage) / 100;
                            } elseif ($service_fee_data->service_fee_type === 'service_fee_custom_amount') {
                                $service_fee = $service_fee_data->service_fee_custom_amount;
                            } elseif ($service_fee_data->service_fee_type === 'none') {
                                $service_fee = 0;
                            } else {
                                $service_fee = 0;
                            }
                            $set('service_fee', $service_fee);
                            $duration = $get('loan_duration') ?? 0;
                            $principle_amount = $get('principal_amount') ?? 0;
                            $disbursement_amount = ($principle_amount -  $service_fee) < 0 ? 0.00 : $principle_amount -  $service_fee;
                            $loan_percent = \App\Models\LoanType::findOrFail($state)->interest_rate ?? 0;
                            $interest_amount = (($principle_amount) * ($loan_percent / 100) * $duration);
                            $total_repayment = ($principle_amount) + (($principle_amount) * ($loan_percent / 100) * $duration);
                            $set('repayment_amount', number_format($total_repayment));
                            $set('interest_amount', number_format($interest_amount));
                            $set('interest_rate', $loan_percent);
                            $set('disbursed_amount', $disbursement_amount);
                            
                            // Auto-set eligibility interest rate from loan type
                            $set('eligibility_interest_rate', $loan_percent);
                            // Recalculate eligibility if monthly pay is already set
                            if ($get('monthly_pay')) {
                                self::recalculateNewPMECEligibility($set, $get);
                            }
                            // Update qualification status
                            self::determineQualificationStatus($set, $get);
                        }
                        return true;
                    }),

                Forms\Components\Select::make('borrower_id')
                    ->prefixIcon('heroicon-o-user')
                    ->relationship('borrower', 'full_name')
                    ->preload()
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                        // When borrower pre-fill their financial data
                        if ($state) {
                            $borrower = \App\Models\Borrower::find($state);
                            if ($borrower) {
                                $set('borrower_monthly_income', $borrower->monthly_income ?? 0);
                                $set('borrower_employment_months', $borrower->employment_months ?? 0);
                                $set('borrower_existing_debts', $borrower->existing_debts ?? 0);
                                $set('borrower_credit_history_months', $borrower->credit_history_months ?? 0);
                                $set('borrower_previous_defaults', $borrower->previous_defaults ?? 0);
                            }
                        }
                        // Update qualification status
                        self::determineQualificationStatus($set, $get);
                    }),
                Forms\Components\Select::make('loan_status')
                    ->label('Loan Status')
                    ->prefixIcon('fas-dollar-sign')
                    ->options([
                        'requested' => 'Requested',
                        'processing' => 'Process with AI',
                        'approved' => 'Approved',
                        'denied' => 'Denied',
                        'defaulted' => 'Defaulted',

                    ])
                    ->required(),
                Forms\Components\TextInput::make('principal_amount')
                    ->label('Principle Amount')
                    ->required()
                    ->prefixIcon('fas-dollar-sign')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                        if ($get('loan_type_id')) {
                            $service_fee = 0.00;
                            $service_fee_data = \App\Models\LoanType::findOrFail($get('loan_type_id'));

                            if ($service_fee_data->service_fee_type === 'service_fee_percentage') {
                                $service_fee = ($state * $service_fee_data->service_fee_percentage) / 100;
                            } elseif ($service_fee_data->service_fee_type === 'service_fee_custom_amount') {
                                $service_fee = $service_fee_data->service_fee_custom_amount;
                            } elseif ($service_fee_data->service_fee_type === 'none') {
                                $service_fee = 0;
                            } else {
                                $service_fee = 0;
                            }
                            $set('service_fee', $service_fee);
                            $duration = $get('loan_duration') ?? 0;
                            $principle_amount = $state ?? 0;
                            $disbursement_amount = ($principle_amount -  $service_fee) < 0 ? 0.00 : $principle_amount -  $service_fee;
                            $loan_percent = \App\Models\LoanType::findOrFail($get('loan_type_id'))->interest_rate ?? 0;
                            $interest_amount = (($principle_amount) * ($loan_percent / 100) * $duration);
                            $total_repayment = ($principle_amount) + (($principle_amount) * ($loan_percent / 100) * $duration);
                            $set('repayment_amount', number_format($total_repayment));
                            $set('interest_amount', number_format($interest_amount));
                            $set('interest_rate', $loan_percent);
                            $set('disbursed_amount', $disbursement_amount);
                        }
                        // Auto-determine qualification status
                        self::determineQualificationStatus($set, $get);
                        return true;
                    })

                    ->numeric(),
                Forms\Components\TextInput::make('loan_duration')
                    ->label('Loan Duration')
                    ->prefixIcon('fas-clock')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                        if ($state && $get('loan_type_id') && $get('principal_amount')) {
                            $duration = $state ?? 0;
                            $principle_amount = $get('principal_amount');
                            $loan_percent = \App\Models\LoanType::findOrFail($get('loan_type_id'))->interest_rate ?? 0;
                            $interest_amount = (($principle_amount) * ($loan_percent / 100) * $duration);
                            $total_repayment = ($principle_amount) + (($principle_amount) * ($loan_percent / 100) * $duration);
                            $set('repayment_amount', number_format($total_repayment));
                            $set('interest_amount', number_format($interest_amount));
                            $set('interest_rate', $loan_percent);
                        }
                        return true;
                    })

                    ->numeric(),
                Forms\Components\TextInput::make('duration_period')
                    ->label('Duration Period')
                    ->prefixIcon('fas-clock')
                    ->required()
                    ->readOnly(),
                Forms\Components\DatePicker::make('loan_release_date')
                    ->label('Loan Release Date')
                    ->prefixIcon('heroicon-o-calendar')
                    ->live(onBlur: true)
                    ->required()
                    ->native(false)
                    ->maxDate(now()),
                Forms\Components\TextInput::make('repayment_amount')
                    ->label('Repayment Amount')
                    ->prefixIcon('fas-coins')
                    ->required()
                    ->readOnly(),
                Forms\Components\TextInput::make('interest_amount')
                    ->label('Interest Amount')
                    ->prefixIcon('fas-coins')
                    ->readOnly()
                    ->required(),

                Forms\Components\TextInput::make('interest_rate')
                    ->label('Interest Rate')
                    ->required()
                    ->prefixIcon('fas-percentage')
                    ->readOnly()
                    ->numeric(),

                Forms\Components\DatePicker::make('loan_due_date')
                    ->label('Loan Due Date')
                    ->prefixIcon('heroicon-o-calendar')
                    ->hidden()
                    ->required()
                    ->native(false),



                Forms\Components\TextInput::make('service_fee')
                    ->label('Processing Fee')
                    ->prefixIcon('fas-percentage')
                    ->readOnly()
                    ->numeric(),

                Forms\Components\TextInput::make('disbursed_amount')
                    ->label('Amount to be Disbursed')
                    ->required()
                    ->prefixIcon('fas-percentage')
                    ->readOnly()
                    ->numeric(),




                Hidden::make('loan_number'),
                Forms\Components\Select::make('from_this_account')
                    ->label('From this Account')
                    ->prefixIcon('fas-wallet')
                    ->options($options->pluck('label', 'value')->toArray())
                    ->required()
                    ->searchable(),

                Forms\Components\TextInput::make('transaction_reference')
                    ->label('Transaction Reference')
                    ->prefixIcon('fas-money-bill-wave'),
                Forms\Components\Toggle::make('activate_loan_agreement_form')
                    ->label('Compile Loan Agreement Form')
                    ->helperText('If you want to compile the loan agreement for this applicant make sure you have added the loan agreement form template for this type of loan.')
                    ->onColor('success')
                    ->offColor('danger')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('loan_agreement_file_path')
                    ->hidden(),
                Forms\Components\TextInput::make('balance')
                    ->hidden(),
                Forms\Components\Section::make('Borrower Financial Information')
                    ->description('This data will be used for AI credit scoring')
                    ->schema([
                        Forms\Components\TextInput::make('borrower_monthly_income')
                            ->label('Monthly Income (ZMW)')
                            ->prefixIcon('fas-money-bill-wave')
                            ->numeric()
                            ->required(),

                        Forms\Components\TextInput::make('borrower_employment_months')
                            ->label('Employment Duration (Months)')
                            ->prefixIcon('fas-briefcase')
                            ->numeric()
                            ->required(),

                        Forms\Components\TextInput::make('borrower_existing_debts')
                            ->label('Existing Debts (ZMW)')
                            ->prefixIcon('fas-scale-balanced')
                            ->numeric()
                            ->required(),

                        Forms\Components\TextInput::make('borrower_credit_history_months')
                            ->label('Credit History Length (Months)')
                            ->prefixIcon('fas-history')
                            ->numeric()
                            ->required(),

                        Forms\Components\TextInput::make('borrower_previous_defaults')
                            ->label('Previous Loan Defaults')
                            ->prefixIcon('fas-exclamation-triangle')
                            ->numeric()
                            ->default(0)
                            ->required(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('PMEC Eligibility Calculator')
                    ->description('ELIGILITY CALCULATOR Based on 60% of Monthly Pay for Civil service clients in Zambia')
                    ->schema([
                        Forms\Components\Placeholder::make('helper_text')
                            ->label('')
                            ->content('💡 Tip: Click outside the input field after entering values to see updated calculations.')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('monthly_pay')
                            ->label('Monthly Pay (ZMW)')
                            ->prefixIcon('fas-dollar-sign')
                            ->numeric()
                            ->default(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                self::recalculateNewPMECEligibility($set, $get);
                                self::determineQualificationStatus($set, $get);
                            })
                            ->helperText('Basic Pay / Monthly Salary. Click outside to update calculations.'),

                        Forms\Components\TextInput::make('maximum_allowable_emi')
                            ->label('Maximum Allowable EMI (ZMW)')
                            ->prefixIcon('fas-calculator')
                            ->numeric()
                            ->readOnly()
                            ->dehydrated()
                            ->helperText('Auto-calculated: 60% of Monthly Pay'),

                        Forms\Components\TextInput::make('existing_loans_emi')
                            ->label('Existing Loans EMI (ZMW)')
                            ->prefixIcon('fas-minus-circle')
                            ->numeric()
                            ->default(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                self::recalculateNewPMECEligibility($set, $get);
                                self::determineQualificationStatus($set, $get);
                            })
                            ->helperText('Total of existing loan deductions. Click outside to update calculations.'),

                        Forms\Components\TextInput::make('eligible_emi')
                            ->label('Eligible EMI (ZMW)')
                            ->prefixIcon('fas-check-circle')
                            ->numeric()
                            ->readOnly()
                            ->dehydrated()
                            ->helperText('Auto-calculated: Maximum Allowable EMI - Existing Loans EMI'),

                        Forms\Components\Select::make('eligibility_interest_rate')
                            ->label('Interest Rate (% per month)')
                            ->prefixIcon('fas-percent')
                            ->options(function () {
                                $loanTypes = \App\Models\LoanType::all();
                                $options = [];
                                foreach ($loanTypes as $loanType) {
                                    $interestRate = (float) ($loanType->interest_rate ?? 0);
                                    $options[$interestRate] = $loanType->loan_name . ' - ' . number_format($interestRate, 2) . '%';
                                }
                                // If no loan types, provide default options
                                if (empty($options)) {
                                    $options = [
                                        2 => '2% per month',
                                        3 => '3% per month',
                                        4 => '4% per month',
                                        5 => '5% per month',
                                    ];
                                }
                                return $options;
                            })
                            ->default(4)
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                self::recalculateNewPMECEligibility($set, $get);
                                self::determineQualificationStatus($set, $get);
                            })
                            ->required()
                            ->searchable()
                            ->helperText('Select interest rate from loan types. Click outside to update calculations.'),

                        Forms\Components\Select::make('loan_period')
                            ->label('Loan Period (Months)')
                            ->options([
                                1 => '1 Month',
                                2 => '2 Months',
                                3 => '3 Months',
                                4 => '4 Months',
                                5 => '5 Months',
                                6 => '6 Months',
                                12 => '12 Months',
                                24 => '24 Months',
                                36 => '36 Months',
                                48 => '48 Months',
                                60 => '60 Months',
                            ])
                            ->default(24)
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                self::recalculateNewPMECEligibility($set, $get);
                                self::determineQualificationStatus($set, $get);
                            })
                            ->required()
                            ->helperText('Select loan period for eligibility calculation'),

                        Forms\Components\TextInput::make('loan_amount_eligibility')
                            ->label('Loan Amount Eligibility (ZMW)')
                            ->prefixIcon('fas-dollar-sign')
                            ->numeric()
                            ->readOnly()
                            ->dehydrated()
                            ->helperText('Auto-calculated based on Eligible EMI, loan period, and selected interest rate'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Select::make('qualification_status')
                    ->label('Qualification Status')
                    ->options([
                        'qualified' => 'Qualified',
                        'not_qualified' => 'Not Qualified',
                        'review_required' => 'Review Required',
                    ])
                    ->default('review_required')
                    ->required()
                    ->disabled()
                    ->dehydrated()
                    ->helperText('Auto-calculated based on eligibility data. Status updates automatically when loan details change.'),

                Forms\Components\Textarea::make('qualification_notes')
                    ->label('Qualification Notes')
                    ->rows(3)
                    ->maxLength(1000)
                    ->helperText('Add any additional notes about the qualification decision')
                    ->columnSpanFull(),

                Forms\Components\Section::make('Loan Documents & Attachments')
                    ->description('Upload all required documents for this loan application')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('payslips')
                            ->label('Payslips')
                            ->disk('borrowers')
                            ->collection('payslips')
                            ->visibility('public')
                            ->multiple()
                            ->minFiles(0)
                            ->maxFiles(10)
                            ->maxSize(5120)
                            ->openable()
                            ->helperText('Upload borrower payslips (max 5MB per file)'),
                        
                        SpatieMediaLibraryFileUpload::make('bank_statements')
                            ->label('Bank Statements')
                            ->disk('borrowers')
                            ->collection('bank_statements')
                            ->visibility('public')
                            ->multiple()
                            ->minFiles(0)
                            ->maxFiles(10)
                            ->maxSize(5120)
                            ->openable()
                            ->helperText('Upload bank statements (max 5MB per file)'),
                        
                        SpatieMediaLibraryFileUpload::make('nrc')
                            ->label('National ID (NRC)')
                            ->disk('borrowers')
                            ->collection('nrc')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->openable()
                            ->helperText('Upload National Registration Card (max 5MB)'),
                        
                        SpatieMediaLibraryFileUpload::make('preapproval_letter')
                            ->label('Pre-approval Letter')
                            ->disk('borrowers')
                            ->collection('preapproval_letter')
                            ->visibility('public')
                            ->minFiles(0)
                            ->maxSize(5120)
                            ->openable()
                            ->helperText('Upload pre-approval letter if available (max 5MB)'),
                        
                        SpatieMediaLibraryFileUpload::make('proof_of_residence')
                            ->label('Proof of Residence')
                            ->disk('borrowers')
                            ->collection('proof_of_residence')
                            ->visibility('public')
                            ->minFiles(0)
                            ->maxSize(5120)
                            ->openable()
                            ->helperText('Upload proof of residence (max 5MB)'),
                        
                        SpatieMediaLibraryFileUpload::make('collaterals')
                            ->label('Collaterals')
                            ->disk('borrowers')
                            ->collection('collaterals')
                            ->visibility('public')
                            ->multiple()
                            ->minFiles(0)
                            ->maxFiles(10)
                            ->maxSize(5120)
                            ->openable()
                            ->helperText('Upload collateral documents (max 5MB per file)'),
                    ])
                    ->columns(2)
                    ->collapsible(),

            ]);
    }

    public static function table(Table $table): Table
    {
        $create_link = new CreateLinks();
        return $table
            ->headerActions([
                ExportAction::make()
                    ->exporter(LoanExporter::class)
            ])
            ->columns([

                Tables\Columns\TextColumn::make('ai_credit_score')
                    ->label('AI Score')
                    ->sortable()
                    ->color(fn($record) => match (true) {
                        $record->ai_credit_score >= 700 => 'success',
                        $record->ai_credit_score >= 600 => 'warning',
                        $record->ai_credit_score >= 500 => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn($record) => $record->ai_scored_at ? 'heroicon-o-cpu-chip' : null)
                    ->tooltip(fn($record) => $record->ai_scored_at ? 'AI Assessed' : 'Not Assessed'),

                Tables\Columns\TextColumn::make('ai_recommendation')
                    ->label('AI Rec')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'APPROVE' => 'success',
                        'REVIEW' => 'warning',
                        'REJECT' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('borrower.full_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('loan_type.loan_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('loan_status')
                    ->badge()

                    ->color(fn(string $state): string => match ($state) {
                        'requested' => 'gray',
                        'processing' => 'info',
                        'approved' => 'success',
                        'fully_paid' => 'success',
                        'denied' => 'danger',
                        'defaulted' => 'warning',
                        default => 'warning',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('principal_amount')
                    ->label('Principle Amount')
                    ->money('ZMW')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('service_fee')
                    ->label('Service Fee')
                    ->money('ZMW')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('disbursed_amount')
                    ->label('Disbursed Amount')
                    ->money('ZMW')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('balance')
                    ->label('Balance')
                    ->money('ZMW')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('loan_due_date')
                    ->label('Due Date')
                    ->searchable(),

                Tables\Columns\TextColumn::make('loan_number')
                    ->label('Loan Number')
                    ->badge()
                    ->searchable(),

                Tables\Columns\TextColumn::make('id')

                    ->label('Loan Statement')
                    ->formatStateUsing(function ($state, $record) {
                        $url = route('statement.download', $record->id);
                        return "<a href='{$url}' target='_blank' class='text-primary underline'>Download</a>";
                    })

                    ->html()
                    ->searchable(),



                Tables\Columns\TextColumn::make('loan_agreement_file_path')
                    ->label('Loan Agreement Form')
                    ->formatStateUsing(

                        fn(string $state) => $create_link::goTo(env('APP_URL') . '/' . $state, 'download', 'loan agreement form'),
                    ),
                Tables\Columns\TextColumn::make('loan_settlement_file_path')
                    ->label('Loan Settlement Form')
                    ->formatStateUsing(

                        fn(string $state) => $create_link::goTo(env('APP_URL') . '/' . $state, 'download', 'loan settlement form'),
                    )
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('loan_status')
                    ->options([
                        'requested' => 'Requested',
                        'processing' => 'Processing',
                        'approved' => 'Approved',
                        'denied' => 'Denied',
                        'defaulted' => 'Defaulted',
                        'partially_paid' => 'Partially Paid',
                        'fully_paid' => 'Fully Paid',

                    ]),

            ])
            ->actions([
                Tables\Actions\Action::make('previewApplication')
                    ->label('Preview Application')
                    ->icon('heroicon-o-document-text')
                    ->color('primary')
                    ->url(fn ($record) => route('loan.application.preview', ['id' => $record->id]))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('downloadApplication')
                    ->label('Download Application')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn ($record) => route('loan.application.download', ['id' => $record->id])),
                Tables\Actions\Action::make('previewMandate')
                    ->label('Preview Direct Debit Mandate')
                    ->icon('heroicon-o-banknotes')
                    ->color('info')
                    ->url(fn ($record) => route('direct.debit.mandate.preview', ['id' => $record->id]))
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record->loan_status === 'approved'),
                Tables\Actions\Action::make('downloadMandate')
                    ->label('Download Direct Debit Mandate')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('warning')
                    ->url(fn ($record) => route('direct.debit.mandate.download', ['id' => $record->id]))
                    ->visible(fn ($record) => $record->loan_status === 'approved'),
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'ai-assessment' => Pages\AIAssessment::route('/{record}/ai-assessment'),
            'cash-flow-statement' => Pages\CashFlowStatement::route('/cash-flow-statement'),
            'emi-schedule' => Pages\EMISchedule::route('/{record}/emi-schedule'),
            'index' => Pages\ListLoans::route('/'),
            'create' => Pages\CreateLoan::route('/create'),
            'view' => Pages\ViewLoan::route('/{record}'),
            'edit' => Pages\EditLoan::route('/{record}/edit'),


        ];
    }

    /**
     * Calculate net pay based on the formula:
     * Net Pay = [(0.60 × Basic Pay) + Total Recurring Allowances] - [PAYE + Pension + Health Insurance + Other Statutory Deductions + Other Recurring Deductions]
     */
    protected static function calculateNetPay(Get $get, ?float $overrideAllowances = null): float
    {
        $basicPay = (float) ($get('basic_pay') ?? 0);
        $allowances = $overrideAllowances ?? (float) ($get('total_recurring_allowances') ?? 0);

        // Add other allowances to the total
        $otherAllowances = self::calculateTotalFromRepeater($get('other_allowances') ?? []);
        $totalAllowances = $allowances + $otherAllowances;

        $paye = (float) ($get('paye') ?? 0);
        $pension = (float) ($get('pension_napsa') ?? 0);
        $healthInsurance = (float) ($get('health_insurance') ?? 0);

        // Calculate 60% of basic pay
        $sixtyPercentBasicPay = $basicPay * 0.60;

        // Sum of income components (including other allowances)
        $totalIncome = $sixtyPercentBasicPay + $totalAllowances;

        // Sum of statutory deductions
        $statutoryDeductions = $paye + $pension + $healthInsurance;

        // Sum of other recurring deductions
        $otherRecurring = self::calculateTotalFromRepeater($get('other_recurring_deductions'));

        // Total deductions
        $totalDeductions = $statutoryDeductions + $otherRecurring;

        // Calculate net pay (can be negative if deductions exceed income)
        $netPay = $totalIncome - $totalDeductions;

        return round($netPay, 2);
    }

    /**
     * Update qualification status based on comparison of calculated vs actual net pay
     */
    protected static function updateQualificationStatus($actualNetPay, $calculatedNetPay, Set $set): void
    {
        if (empty($actualNetPay) || empty($calculatedNetPay)) {
            $set('qualification_status', 'review_required');
            return;
        }

        $actual = (float) $actualNetPay;
        $calculated = (float) $calculatedNetPay;

        // Calculate percentage variance
        $variance = abs($actual - $calculated);
        $percentageVariance = $calculated > 0 ? ($variance / $calculated) * 100 : 0;

        if ($percentageVariance < 5) {
            // Less than 5% variance - qualified
            $set('qualification_status', 'qualified');
        } elseif ($percentageVariance >= 5 && $percentageVariance < 15) {
            // Greater than or equal to 5% and less than 15% variance - review required
            $set('qualification_status', 'review_required');
        } else {
            // Greater than or equal to 15% variance - not qualified
            $set('qualification_status', 'not_qualified');
        }
    }

    /**
     * Recalculate civil service net pay, sync totals, and update qualification status.
     */
    protected static function recalculateCivilServiceNetPay(Set $set, Get $get, ?float $overrideAllowances = null): void
    {
        // Recalculate includes other allowances automatically in calculateNetPay
        $netPay = self::calculateNetPay($get, $overrideAllowances);
        $set('calculated_net_pay', $netPay);
    }

    /**
     * Recalculate New PMEC Eligibility based on formula from "ELIGILITY CALCULATOR plus (2).csv":
     * Maximum Allowable EMI = 60% of Monthly Pay
     * Eligible EMI = Maximum Allowable EMI - Existing Loans EMI
     * Loan Amount Eligibility = Based on Eligible EMI and loan period
     */
    protected static function recalculateNewPMECEligibility(Set $set, Get $get): void
    {
        $monthlyPay = (float) ($get('monthly_pay') ?? 0);
        $existingLoansEMI = (float) ($get('existing_loans_emi') ?? 0);
        
        // Calculate 60% of monthly pay
        $maximumAllowableEMI = $monthlyPay * 0.60;
        $set('maximum_allowable_emi', round($maximumAllowableEMI, 2));
        
        // Eligible EMI = Maximum Allowable EMI - Existing Loans EMI
        $eligibleEMI = $maximumAllowableEMI - $existingLoansEMI;
        $set('eligible_emi', round($eligibleEMI, 2));
        
        // Calculate Loan Amount Eligibility based on Eligible EMI and loan period
        $period = (int) ($get('loan_period') ?? 24);
        $monthlyInterestRate = (float) ($get('eligibility_interest_rate') ?? 4.0);
        
        if ($eligibleEMI > 0 && $period > 0 && $monthlyInterestRate > 0) {
            // Use the selected interest rate from loan types
            $totalInterestRate = $monthlyInterestRate * $period;
            
            // Formula: Loan Amount = (Eligible EMI * Period) / (1 + (Total Interest Rate / 100))
            // This calculates the maximum loan amount that can be serviced by the Eligible EMI
            $denominator = 1 + ($totalInterestRate / 100);
            $loanAmountEligibility = ($eligibleEMI * $period) / $denominator;
            $set('loan_amount_eligibility', round($loanAmountEligibility, 2));
        } else {
            $set('loan_amount_eligibility', 0);
        }
        
        // Auto-determine qualification status after eligibility calculation
        self::determineQualificationStatus($set, $get);
    }

    /**
     * Automatically determine qualification status based on eligibility data
     * Rules:
     * - Qualified: Principal amount <= Loan amount eligibility AND Eligible EMI > 0 AND all required data present
     * - Not Qualified: Principal amount > Loan amount eligibility OR Eligible EMI <= 0
     * - Review Required: Missing required data or edge cases
     */
    protected static function determineQualificationStatus(Set $set, Get $get): void
    {
        $principalAmount = (float) ($get('principal_amount') ?? 0);
        $loanAmountEligibility = (float) ($get('loan_amount_eligibility') ?? 0);
        $eligibleEMI = (float) ($get('eligible_emi') ?? 0);
        $monthlyPay = (float) ($get('monthly_pay') ?? 0);
        $loanTypeId = $get('loan_type_id');
        $borrowerId = $get('borrower_id');
        
        // Check if required data is missing
        $hasRequiredData = $loanTypeId && $borrowerId && $monthlyPay > 0 && $principalAmount > 0;
        
        // If required data is missing, set to review required
        if (!$hasRequiredData) {
            $set('qualification_status', 'review_required');
            return;
        }
        
        // If eligible EMI is negative or zero, not qualified
        if ($eligibleEMI <= 0) {
            $set('qualification_status', 'not_qualified');
            return;
        }
        
        // If loan amount eligibility is zero or not calculated, review required
        if ($loanAmountEligibility <= 0) {
            $set('qualification_status', 'review_required');
            return;
        }
        
        // Calculate percentage of eligibility used
        $eligibilityPercentage = ($principalAmount / $loanAmountEligibility) * 100;
        
        // Determine qualification based on principal amount vs eligibility
        if ($principalAmount <= $loanAmountEligibility) {
            // Within eligibility - qualified
            // Allow up to 100% of eligibility
            if ($eligibilityPercentage <= 100) {
                $set('qualification_status', 'qualified');
            } else {
                // Slightly over (up to 105%) - review required
                $set('qualification_status', 'review_required');
            }
        } else {
            // Exceeds eligibility - not qualified
            // If over 105% of eligibility, definitely not qualified
            if ($eligibilityPercentage > 105) {
                $set('qualification_status', 'not_qualified');
            } else {
                // Between 100-105% - review required
                $set('qualification_status', 'review_required');
            }
        }
    }

    /**
     * Helper to sum amounts from repeater state arrays.
     */
    protected static function calculateTotalFromRepeater(?array $items): float
    {
        return collect($items ?? [])->sum(fn ($item) => (float) ($item['amount'] ?? 0));
    }
}
