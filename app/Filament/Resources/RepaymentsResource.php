<?php

namespace App\Filament\Resources;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Filament\Forms\Set;
use Filament\Forms\Get;
use App\Filament\Resources\RepaymentsResource\Pages;
use App\Filament\Resources\RepaymentsResource\RelationManagers;
use App\Models\Repayments;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RepaymentsResource extends Resource
{
    protected static ?string $model = Repayments::class;

    protected static ?string $navigationGroup = 'Repayments';
    protected static ?string $navigationIcon = 'fas-dollar-sign';
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
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
                    ->live(onBlur: true)
                    ->required(function ($state, Set $set) {
                        if ($state) {
                            $balance = \App\Models\Loan::findOrFail($state)->balance;
                            $set('balance', $balance);
                        }
                        return true;
                    }),



                Forms\Components\TextInput::make('payments')
                    ->label('Repayment Amount')
                    ->prefixIcon('fas-dollar-sign')
                    ->required(),
                Forms\Components\TextInput::make('balance')
                    ->label('Current Balance')
                    ->prefixIcon('fas-dollar-sign')
                    ->readOnly(),

                Forms\Components\Select::make('payments_method')
                    ->label('Payment Method')
                    ->prefixIcon('fas-dollar-sign')
                    ->required()
                    ->options([
                        'bank_transfer' => 'Bank Transfer',
                        'mobile_money' => 'Mobile Money',
                        'pemic' => 'PEMIC',
                        'cheque' => 'Cheque',
                        'cash' => 'Cash',

                    ]),
                Forms\Components\TextInput::make('reference_number')
                    ->label('Transaction Reference')
                    ->prefixIcon('fas-dollar-sign')
                    ->columnSpan(2),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
         ->recordUrl(null)
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                ->label('Payments Date')
                    ->searchable(),
                    Tables\Columns\TextColumn::make('reference_number')
                    ->label('Reference Number')
                        ->searchable(),
                Tables\Columns\TextColumn::make('loan_number.loan_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('loan_number.loan_status')
                ->label('Loan Status')
                ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('payments')
                    ->searchable(),
                    // Tables\Columns\TextColumn::make('loan_number.repayment_amount')
                    // ->label('Total Repayments')
                    // ->searchable(),
                Tables\Columns\TextColumn::make('balance')
                    ->searchable(),
                   
                  
            ])
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
            ->actions([
                // Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListRepayments::route('/'),
            'create' => Pages\CreateRepayments::route('/create'),
            'view' => Pages\ViewRepayments::route('/{record}'),
            'edit' => Pages\EditRepayments::route('/{record}/edit'),
        ];
    }
}
