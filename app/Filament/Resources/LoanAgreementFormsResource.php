<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanAgreementFormsResource\Pages;
use Filament\Forms\Components\RichEditor;
use App\Filament\Resources\LoanAgreementFormsResource\RelationManagers;
use App\Models\LoanAgreementForms;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LoanAgreementFormsResource extends Resource
{
    protected static ?string $model = LoanAgreementForms::class;

    protected static ?string $navigationIcon = 'fas-file';
    protected static ?string $navigationGroup = 'Loan Agreement Forms';
    
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
                ->label('Choose Loan Type')
                ->relationship('loan_type', 'loan_name')
                ->helperText('Please make sure you have added the loan type under loans to create the loan agreement form.')
                ->searchable()
                ->columnSpan(2)
                ->preload(), 
                RichEditor::make('loan_agreement_text')
                ->label('Create Form')
                ->placeholder('John Doe')
                ->columnSpan(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
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
            'index' => Pages\ListLoanAgreementForms::route('/'),
            'create' => Pages\CreateLoanAgreementForms::route('/create'),
            'view' => Pages\ViewLoanAgreementForms::route('/{record}'),
            'edit' => Pages\EditLoanAgreementForms::route('/{record}/edit'),
        ];
    }    
}
