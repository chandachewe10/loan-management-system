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
                    ->afterStateUpdated(function ($state, Set $set) {
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

                Forms\Components\Section::make('For Civil Service Clients')
                    ->description('Loan qualification calculator based on payslip analysis. Net Pay = [(0.60 × Basic Pay) + Total Recurring Allowances] - [PAYE + Pension + Health Insurance + Other Recurring Deductions]')
                    ->schema([
                        Forms\Components\TextInput::make('basic_pay')
                            ->label('Basic Pay (ZMW)')
                            ->prefixIcon('fas-dollar-sign')
                            ->numeric()
                            ->default(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                self::recalculateCivilServiceNetPay($set, $get);
                            }),

                        Forms\Components\TextInput::make('total_recurring_allowances')
                            ->label('Total Recurring Allowances (ZMW)')
                            ->prefixIcon('fas-plus-circle')
                            ->numeric()
                            ->default(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                self::recalculateCivilServiceNetPay($set, $get);
                            })
                            ->helperText('Enter the total recurring allowances amount'),
                            

                        Forms\Components\Repeater::make('other_allowances')
                            ->label('Other Allowances')
                            ->schema([
                                Forms\Components\TextInput::make('description')
                                    ->label('Description')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('amount')
                                    ->label('Amount (ZMW)')
                                    ->numeric()
                                    ->required()
                                    ->default(0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        self::recalculateCivilServiceNetPay($set, $get);
                                    }),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Add Other Allowance')
                            ->addAction(fn ($action) => $action->color('success'))
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['description'] ?? null)
                            ->columnSpanFull()
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                self::recalculateCivilServiceNetPay($set, $get);
                            }),

                        Forms\Components\TextInput::make('paye')
                            ->label('PAYE (ZMW)')
                            ->prefixIcon('fas-minus-circle')
                            ->numeric()
                            ->default(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                self::recalculateCivilServiceNetPay($set, $get);
                            }),

                        Forms\Components\TextInput::make('pension_napsa')
                            ->label('Pension/NAPSA (ZMW)')
                            ->prefixIcon('fas-minus-circle')
                            ->numeric()
                            ->default(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                self::recalculateCivilServiceNetPay($set, $get);
                            }),

                        Forms\Components\TextInput::make('health_insurance')
                            ->label('Health Insurance (ZMW)')
                            ->prefixIcon('fas-heartbeat')
                            ->numeric()
                            ->default(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                self::recalculateCivilServiceNetPay($set, $get);
                            }),

                        Forms\Components\Repeater::make('other_recurring_deductions')
                            ->label('Other Recurring Deductions (Including 3rd Party Deductions)')
                            ->schema([
                                Forms\Components\TextInput::make('description')
                                    ->label('Description')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('amount')
                                    ->label('Amount (ZMW)')
                                    ->numeric()
                                    ->required()
                                    ->default(0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        self::recalculateCivilServiceNetPay($set, $get);
                                    }),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Add Recurring Deduction')
                            ->addAction(fn ($action) => $action->color('primary'))
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['description'] ?? null)
                            ->columnSpanFull()
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                self::recalculateCivilServiceNetPay($set, $get);
                            }),

                        Forms\Components\TextInput::make('calculated_net_pay')
                            ->label('Calculated Net Pay (ZMW)')
                            ->prefixIcon('fas-calculator')
                            ->numeric()
                            ->readOnly()
                            ->dehydrated()
                            ->helperText('Auto-calculated: [(0.60 × Basic Pay) + Allowances] - [All Deductions]'),

                        Forms\Components\Select::make('qualification_status')
                            ->label('Qualification Status')
                            ->options([
                                'qualified' => 'Qualified',
                                'not_qualified' => 'Not Qualified',
                                'review_required' => 'Review Required',
                            ])
                            ->default('review_required')
                            ->required()
                            ->dehydrated()
                            ->helperText('Manually set the qualification status based on the calculated net pay and payslip review'),

                        Forms\Components\Textarea::make('qualification_notes')
                            ->label('Qualification Notes')
                            ->rows(3)
                            ->maxLength(1000)
                            ->helperText('Add any additional notes about the qualification decision'),
                    ])
                    ->columns(2)
                    ->collapsible(),

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
     * Helper to sum amounts from repeater state arrays.
     */
    protected static function calculateTotalFromRepeater(?array $items): float
    {
        return collect($items ?? [])->sum(fn ($item) => (float) ($item['amount'] ?? 0));
    }
}
