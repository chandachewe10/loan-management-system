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


class LoanAgreementFormsResource extends Resource
{
    protected static ?string $model = LoanAgreementForms::class;

    protected static ?string $navigationIcon = 'fas-file';
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
                Forms\Components\Select::make('loan_type_id')
                    ->prefixIcon('heroicon-o-wallet')
                    ->label('Choose Loan Type')
                    ->required()
                    ->relationship('loan_type', 'loan_name')
                    ->helperText('Please make sure you have added the loan type under loans to create the loan agreement form.')
                    ->searchable()
                    ->columnSpan(2)
                    ->preload(),
                RichEditor::make('loan_agreement_text')
                    ->label('Create Form')
                    ->required()
                    ->disableToolbarButtons([
                        'attachFiles',
                        'codeBlock',
                    ])
                    
                    ->default('<p>[Company Name]</p><p>Lusaka Zambia</p><p>P.O BOX 1209,</p><p>Lusaka</p><p>10/09/09&nbsp;</p><p><br></p><p>Dear [Borrower Name],</p><h3><span style="text-decoration: underline;">REF:LOAN AGREEMENT FORM</span></h3><p>Dear [Borrower Name],</p><p>This agreement is made between [Company Name], referred to as the "Lender," and [Borrower Name], whose details are as follows:</p><ul><li>Name: [Borrower Name]</li><li>Email: [Borrower Email]</li><li>Phone: [Borrower Phone]</li><li>Loan Number: [Loan Number]</li><li>Loan Amount: [Loan Amount]</li><li>Loan Tenure: [Loan Tenure]</li><li>Loan Interest Percentage: [Loan Interest Percentage]</li><li>Loan Interest Fee: [Loan Interest Fee]</li></ul><p>The lender agrees to provide a loan of [Loan Amount] to the borrower under the following terms and conditions:............................</p><p>The borrower agrees to repay the loan amount in installments within the loan tenure period. By signing this agreement, the borrower acknowledges and agrees to the terms and conditions set forth herein.&nbsp;</p><p>Sincerely,</p><p>[Company Name]</p>')
                    ->columnSpan(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('loan_type.loan_name')
                    ->label('Loan Type')
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
            'index' => Pages\ListLoanAgreementForms::route('/'),
            'create' => Pages\CreateLoanAgreementForms::route('/create'),
            'view' => Pages\ViewLoanAgreementForms::route('/{record}'),
            'edit' => Pages\EditLoanAgreementForms::route('/{record}/edit'),
        ];
    }
}
