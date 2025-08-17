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
            ->schema([]);
    }





    public static function table(Table $table): Table
    {




        $create_link = new CreateLinks();
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->where('loan_status', 'defaulted');
            })

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
                        $currentDueDate = Carbon::parse($record->loan_due_date);
                        $form->fill([
                            'current_balance' => $record->balance,
                            'current_due_date' => $currentDueDate->format('Y-m-d'),
                            'new_interest_rate' => $record->interest_rate,
                        ]);
                    })
                    ->form([


                        Forms\Components\TextInput::make('current_balance')
                            ->label('Current Balance')
                            ->prefixIcon('heroicon-o-banknotes')
                            ->disabled()
                            ->numeric()
                            ->required(),
                        Hidden::make('current_balance'),

                        Forms\Components\TextInput::make('current_due_date')
                            ->label('Current Due Date')
                            ->disabled()
                            ->prefixIcon('heroicon-o-calendar')
                            ->required(),
                        Hidden::make('current_due_date'),

                        Forms\Components\TextInput::make('new_interest_rate')
                            ->label('Initial Interest Rate (%)')
                            ->prefixIcon('heroicon-o-arrow-path')
                            ->numeric()
                            ->disabled()
                            ->required(),
                        Hidden::make('new_interest_rate'),

                        Forms\Components\TextInput::make('new_duration')
                            ->label('Extended Duration (months)')
                            ->numeric()
                            ->prefixIcon('heroicon-o-calendar-days')
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {

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
                            ->prefixIcon('heroicon-o-credit-card')
                            ->required(),
                        Hidden::make('new_balance'),

                        Forms\Components\TextInput::make('new_interest_amount')
                            ->label('New Interest Amount')
                            ->prefixIcon('heroicon-o-credit-card')
                            ->numeric()
                            ->disabled()
                            ->required(),
                        Hidden::make('new_interest_amount'),





                        Forms\Components\DatePicker::make('new_due_date')
                            ->label('New Due Date')
                            ->prefixIcon('heroicon-o-calendar-days')
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
                            'interest_amount' => $data['new_interest_amount'],
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
