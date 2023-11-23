<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanTypeResource\Pages;
use App\Filament\Resources\LoanTypeResource\RelationManagers;
use App\Models\LoanType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LoanTypeResource extends Resource
{
    protected static ?string $model = LoanType::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar'; 

    protected static ?string $navigationGroup = 'Loans';

    public static function getNavigationBadge(): ?string
{
    return static::getModel()::count();
}
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('loan_name')
                ->label('Loan Name')
                ->prefixIcon('fas-dollar-sign')
                ->required()
                ->maxLength(255),
                Forms\Components\TextInput::make('interest_rate')
                ->label('Interest Rate')
                ->prefixIcon('fas-percentage')
                ->required()
                ->numeric()
               ,
            Forms\Components\Select::make('interest_cycle')
                ->label('Interest Cycle')
                ->prefixIcon('fas-sync-alt')
                ->options([
                    'daily' => 'Daily',
                    'weekly' => 'Weekly',
                    'monthly' => 'Monthly',
                    'yearly' => 'Yearly',

                ])
                ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('loan_name')
                ->searchable(),
            Tables\Columns\TextColumn::make('interest_rate')
            ->label('Interest Rate (%)')
                ->searchable(),
            Tables\Columns\TextColumn::make('interest_cycle')
                ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('interest_cycle')
                ->options([
                    'daily' => 'Daily',
                    'weekly' => 'Weekly',
                    'monthly' => 'Monthly',
                    'yearly' => 'Yearly',

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
            'index' => Pages\ListLoanTypes::route('/'),
            'create' => Pages\CreateLoanType::route('/create'),
            'view' => Pages\ViewLoanType::route('/{record}'),
            'edit' => Pages\EditLoanType::route('/{record}/edit'),
        ];
    }    
}
