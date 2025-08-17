<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanRestructureResource\Pages;
use App\Filament\Resources\LoanRestructureResource\RelationManagers;
use App\Models\Loan as LoanRestructure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;
use App\helpers\CreateLinks;
use App\Filament\Exports\LoanExporter;
use Filament\Tables\Actions\ExportAction;
use Bavix\Wallet\Models\Wallet;
use Filament\Forms\Components\Hidden;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;



class LoanRestructureResource extends Resource
{
    protected static ?string $model = LoanRestructure::class;

    protected static ?string $navigationGroup = 'Loans';
    protected static ?string $navigationIcon = 'fas-file';
    protected static ?string $recordTitleAttribute = 'Loan Restructure';
    protected static ?string $modelLabel = 'Loan Restructure';
    protected static ?string $pluralModelLabel = 'Loan Restructure';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form

    {
        $options = Wallet::where('organization_id', "=", auth()->user()->organization_id)->get()->map(function ($wallet) {
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
                    ->required(),
                Forms\Components\Select::make('loan_status')
                    ->label('Loan Status')
                    ->prefixIcon('fas-dollar-sign')
                    ->options([
                        'requested' => 'Requested',
                        'processing' => 'Processing',
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
                    ->helperText('If you want to compile the loan agreement for this applicant make sure you have added the loan loan agreement form template for this type of loan.')
                    ->onColor('success')
                    ->offColor('danger')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('loan_agreement_file_path')
                    ->hidden(),
                Forms\Components\TextInput::make('balance')
                    ->hidden(),

            ]);
    }





    public static function table(Table $table): Table
    {




        $create_link = new CreateLinks();
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->where('loan_status', 'defaulted');
            })
            ->headerActions([
                ExportAction::make()
                    ->exporter(LoanExporter::class)
            ])
            ->columns([

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

                Tables\Actions\Action::make('restructure')
                    ->label('Restructure Loan')
                    // ->icon('heroicon-o-adjustments')
                    ->color('warning')
                    ->mountUsing(function (Forms\ComponentContainer $form, Model $record) {
                        $form->fill([
                            'current_balance' => $record->balance,
                            'current_due_date' => $record->loan_due_date->format('Y-m-d'),
                            'new_interest_rate' => $record->interest_rate,
                        ]);
                    })
                    ->form([


                        Forms\Components\TextInput::make('current_balance')
                            ->label('Current Balance')

                            ->disabled()
                            ->numeric()
                            ->required(),
                             Hidden::make('current_balance'),

                        Forms\Components\TextInput::make('current_due_date')
                            ->label('Current Due Date')
                            ->disabled()

                            ->required(),
                             Hidden::make('current_due_date'),

                        Forms\Components\TextInput::make('new_interest_rate')
                            ->label('Initial Interest Rate (%)')
                            ->numeric()
                            ->disabled()
                            ->required(),
                             Hidden::make('new_interest_rate'),

                        Forms\Components\TextInput::make('new_duration')
                            ->label('Extended Duration (months)')
                            ->numeric()
                            ->live()
                            ->afterStateUpdated(function ($state,Set $set, Get $get) {

                                $currentBalance = (float) $get('current_balance') ?? 0;
                                $newDuration = (float) $get('new_duration') ?? 0;
                                $newRate = (float) $get('new_interest_rate') ?? 0;

                                $interestAmount = $currentBalance * ($newRate / 100) * ($newDuration);

                                $restructuredBalance = str_replace(',', '', number_format($currentBalance + $interestAmount, 2));
                                $formatted = number_format($restructuredBalance, 2);
                                $numericValue = str_replace(',', '', $formatted);
                                $currentDueDate = $get('current_due_date') ? Carbon::parse($get('current_due_date')) : now();
                                $newDueDate = $currentDueDate->copy()->addMonths($newDuration);

                                $set('new_balance', $numericValue);
                                $set('new_due_date', $newDueDate->format('Y-m-d'));

                                $set('new_balance', $numericValue);
                                $set('new_interest_amount', $interestAmount);
                                $set('new_duration', $state);
                                return true;
                            })
                            ->required(),

                        Forms\Components\TextInput::make('new_balance')
                            ->label('New Balance')
                            ->numeric()
                            ->disabled()
                            ->required(),
                             Hidden::make('new_balance'),

                             Forms\Components\TextInput::make('new_interest_amount')
                            ->label('New Interest Amount')
                            ->numeric()
                            ->disabled()
                            ->required(),
                             Hidden::make('new_interest_amount'),





                        Forms\Components\DatePicker::make('new_due_date')
                            ->label('New Due Date')
                            ->disabled()
                            ->required(),
                            Hidden::make('new_due_date'),

                        Forms\Components\Textarea::make('restructure_reason')
                            ->label('Reason for Restructuring')
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                       // dd($data);
                        // Save original terms before modifying
                        $record->update([
                            'loan_duration' => $data['new_duration'],
                            'interest_rate' => $data['new_interest_amount'],
                            'balance' => $data['new_balance'],
                            'loan_status' => 'approved',
                            'loan_due_date' => Carbon::parse($data['new_due_date']),
                        ]);



                       // You might want to add notification logic here
                        Notification::make()
                            ->title('Loan Restructured Successfully and has been moved to Active Loans')
                            ->success()
                            ->persistent()
                            ->send();
                    })
                    ->visible(fn($record) => $record->loan_status === 'defaulted'),


            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListLoanRestructures::route('/'),
            'create' => Pages\CreateLoanRestructure::route('/create'),
            'edit' => Pages\EditLoanRestructure::route('/{record}/edit'),
        ];
    }
}
