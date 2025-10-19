<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanRollOverResource\Pages;
use App\Filament\Resources\LoanRollOverResource\RelationManagers;
use App\Models\Repayments as LoanRollOver;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Set;
use Filament\Forms\Get;
use App\Filament\Exports\RepaymentsExporter;
use Filament\Tables\Actions\ExportAction;
use Carbon\Carbon;

class LoanRollOverResource extends Resource
{
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    protected static ?string $navigationUrl = 'create';
    protected static ?string $model = LoanRollOver::class;
    protected static ?string $navigationGroup = 'Loans';
    protected static ?string $navigationIcon = 'fas-copy';
    protected static ?string $recordTitleAttribute = 'Loan Roll-Over';
    protected static ?string $modelLabel = 'Loan Roll-Over';
    protected static ?string $pluralModelLabel = 'Loan Roll-Over';
    protected static ?int $navigationSort = 3;

    public static function getNavigationUrl(): string
    {
        return static::getUrl('create');
    }

    public static function form(Form $form): Form
    {
        return $form


            ->schema([

                Forms\Components\Select::make('loan_id')
                    ->label('Loan Number')
                    ->prefixIcon('heroicon-o-wallet')
                    ->relationship('loan_number', 'loan_number')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required(function ($state, Set $set) {
                        if ($state) {
                            $loanStatus = \App\Models\Loan::findOrFail($state);
                            $currentDueDate = Carbon::parse($loanStatus->loan_due_date);
                            $set('new_due_date', $currentDueDate->copy()->addMonths(1)->format('Y-m-d'));
                            $set('balance', $loanStatus->balance);
                            $set('payments', $loanStatus->interest_amount);
                            $set('current_due_date',  $currentDueDate->format('Y-m-d'));
                        }
                        return true;
                    }),



                Forms\Components\TextInput::make('payments')
                    ->label('Repayment Amount')
                    ->helperText('Customer pays the accrued interest to extend the loan term by one month (loan rollover). The balance remains the same')
                    ->prefixIcon('heroicon-o-credit-card')
                    ->readOnly()
                    ->required(),
                Forms\Components\TextInput::make('balance')
                    ->label('Current Balance')
                   ->prefixIcon('heroicon-o-banknotes')
                    ->readOnly(),

                Forms\Components\Select::make('payments_method')
                    ->label('Payment Method')
                    ->prefixIcon('heroicon-o-currency-pound')
                    ->required()
                    ->options([
                        'bank_transfer' => 'Bank Transfer',
                        'mobile_money' => 'Mobile Money',
                        'pemic' => 'PEMIC',
                        'cheque' => 'Cheque',
                        'cash' => 'Cash',

                    ]),
               
                Forms\Components\TextInput::make('current_due_date')
                    ->label('Current Due Date')
                    ->prefixIcon('fas-calendar-alt')
                    ->readOnly(),

                Forms\Components\TextInput::make('new_due_date')
                    ->label('New Due Date')
                    ->prefixIcon('heroicon-o-calendar-date-range')
                    ->readOnly()


            ])->columns(2);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([])
            ->filters([
                Tables\Filters\SelectFilter::make('payments_method')
                    ->options([
                        'bank_transfer' => 'Bank Transfer',
                        'mobile_money' => 'Mobile Money',
                        'pemic' => 'PEMIC',
                        'cheque' => 'Cheque',
                        'cash' => 'Cash',


                    ]),
            ])
            ->actions([])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListLoanRollOvers::route('/'),
            'create' => Pages\CreateLoanRollOver::route('/create'),
            'edit' => Pages\EditLoanRollOver::route('/{record}/edit'),
        ];
    }
}
