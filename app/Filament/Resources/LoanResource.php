<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms\Set;
use Filament\Forms\Get;
use App\Filament\Resources\LoanResource\Pages;
use App\Filament\Resources\LoanResource\RelationManagers;
use App\Models\Loan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LoanResource extends Resource
{
    protected static ?string $model = Loan::class;
    
    protected static ?string $navigationGroup = 'Loans';
    protected static ?string $navigationIcon = 'fas-dollar-sign'; 
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('loan_type_id')
                    ->prefixIcon('heroicon-o-wallet')
                    ->relationship('loan_type', 'loan_name')
                    ->searchable()
                    ->preload()

                    ->live(onBlur: true)
                    ->required(function ($state, Set $set) {
                        if ($state) {
                            $interest_cycle = \App\Models\LoanType::findOrFail($state)->interest_cycle;
                            $set('duration_period', $interest_cycle);
                        }
                        return true;
                    }),

                Forms\Components\Select::make('borrower_id')
                    ->prefixIcon('heroicon-o-user')
                    ->relationship('borrower', 'full_name')
                    ->preload()
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
                    ->prefixIcon('fas-dollar-sign')
                    ->live(onBlur: true)
                    ->required(function ($state, Set $set, Get $get) {
                        if ($get('loan_type_id')) {
                            $duration = $get('loan_duration') ?? 0;
                            $principle_amount = $state ?? 0;
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
                    Forms\Components\TextInput::make('loan_duration')
                    ->label('Loan Duration')
                    ->prefixIcon('fas-clock')
                    ->live(onBlur: true)
                    ->required(function ($state, Set $set, Get $get) {
                        if($state && $get('loan_type_id') && $get('principal_amount')){
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
                    ->disabled(),
                Forms\Components\DatePicker::make('loan_release_date')
                    ->label('Loan Release Date')
                    ->prefixIcon('heroicon-o-calendar')
                    ->live() 
                    ->required()
                    ->native(false)
                    ->maxDate(now()),
                


                Forms\Components\TextInput::make('repayment_amount')
                    ->label('Repayment Amount')
                    ->prefixIcon('fas-coins')
                    ->required()
                    ->disabled(),
                Forms\Components\TextInput::make('interest_amount')
                    ->label('Interest Amount')
                    ->prefixIcon('fas-coins')
                    ->disabled()
                    ->required(),

                Forms\Components\TextInput::make('interest_rate')
                    ->label('Interest Rate')
                    ->required()
                    ->prefixIcon('fas-percentage')
                    ->disabled()
                    ->numeric(),

                Forms\Components\DatePicker::make('loan_due_date')
                    ->label('Loan Due Date')
                    ->prefixIcon('heroicon-o-calendar')
                    ->hidden()
                    ->required()
                    ->native(false),
                    
                Forms\Components\TextInput::make('transaction_reference')
                    ->label('Transaction Reference')
                    ->prefixIcon('fas-money-bill-wave')

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('borrower.full_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('loan_type.loan_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('loan_status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('principal_amount')
                    ->label('Principle Amount')
                    ->money('ZMW')
                    ->sortable()
                    ->searchable(),
                    Tables\Columns\TextColumn::make('loan_due_date')
                    ->label('Due Date')
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
           
           
            'index' => Pages\ListLoans::route('/'),
            'active' => Pages\ActiveLoans::route('/active'),
            'pending' => Pages\PendingLoans::route('/pending'),
            'denied' => Pages\DeniedLoans::route('/denied'),
            'defaulted' => Pages\DefaultedLoans::route('/defaulted'),
            'partially_paid' => Pages\PartiallyPaidLoans::route('/partially_paid'),
            'fully_paid' => Pages\FullyPaidLoans::route('/fully_paid'),
            'create' => Pages\CreateLoan::route('/create'),
            'view' => Pages\ViewLoan::route('/{record}'),
            'edit' => Pages\EditLoan::route('/{record}/edit'),
            
        ];
    }

    
}
