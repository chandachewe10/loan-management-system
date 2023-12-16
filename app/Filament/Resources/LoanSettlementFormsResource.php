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
                    
                     ->default('<p>Company Name: {company_name}<br>Company Address: {company_address}<br><br>Date: {current_date}<br><br>Customer Name: {customer_name}<br>Customer Address: {customer_address}<br><br>Dear {customer_name},<br><br>We are pleased to inform you that your loan with us has been fully settled. The details of the settlement are as follows:<br><br>Loan Amount: {loan_amount}<br>Settled Date: {settled_date}<br><br>Thank you for choosing our services. If you have any questions or require further assistance, please feel free to contact us.<br><br>Sincerely,<br><br>{company_name}<br>{company_address}<br><br></p>')
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
