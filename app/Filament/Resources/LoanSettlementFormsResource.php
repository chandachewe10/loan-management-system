<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanSettlementFormsResource\Pages;
use App\Filament\Resources\LoanSettlementFormsResource\RelationManagers;
use App\Models\LoanSettlementForms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LoanSettlementFormsResource extends Resource
{
    protected static ?string $model = LoanSettlementForms::class;

    protected static ?string $navigationIcon = 'fas-copy';
       protected static ?string $navigationGroup = 'Loan Agreement Forms';
    protected static ?int $navigationSort = 2; 
    
    

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                RichEditor::make('loan_settlement_text')
                    ->label('Create Settlement Form')
                    ->required()
                   
                    ->disableToolbarButtons([
                        'attachFiles',
                        'codeBlock',
                    ])
                    
                    ->default('<p>{company_name}</p><p>Lusaka Zambia</p><p>P.O BOX 1209,</p><p>Lusaka</p><p>{current_date}</p><p><br></p><p>Dear {customer_name},</p><h3><span style="text-decoration: underline;">REF:LOAN SETTLEMENT FORM</span></h3><p>We are pleased to inform you that your loan with us has been fully settled. The details of the settlement are as follows:</p><ul><li>Loan Amount: {loan_amount}</li><li>Settled Date: {settled_date}</li></ul><p>The lender agrees to provide a loan of {loan_amount} to the borrower under the following terms and conditions:............................</p><p>The borrower agrees to repay the loan amount in installments within the loan tenure period. By signing this agreement, the borrower acknowledges and agrees to the terms and conditions set forth herein.&nbsp;</p><p>Sincerely,</p><p>{company_name}<br>{company_address}</p>')
                    ->columnSpan(2),

                    
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Template No')
                    ->searchable(),
                   
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
            'index' => Pages\ListLoanSettlementForms::route('/'),
            'create' => Pages\CreateLoanSettlementForms::route('/create'),
            'view' => Pages\ViewLoanSettlementForms::route('/{record}'),
            'edit' => Pages\EditLoanSettlementForms::route('/{record}/edit'),
        ];
    }    
}
