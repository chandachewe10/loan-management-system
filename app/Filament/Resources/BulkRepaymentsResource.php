<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BulkRepaymentsResource\Pages;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use App\Models\Repayments;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Exports\RepaymentsExporter;
use Filament\Tables\Actions\ExportAction;


class BulkRepaymentsResource extends Resource
{
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    protected static ?string $model = Repayments::class;


    protected static ?string $navigationLabel = 'Bulk Repayments';
    protected static ?string $modelLabel = 'Bulk Repayments';
    protected static ?string $recordTitleAttribute = 'Bulk Repayments';
    protected static ?string $title = 'Bulk Repayments';
    protected static ?string $navigationGroup = 'Repayments';
    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ExportAction::make()
                    ->exporter(RepaymentsExporter::class)
            ])
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
            'index' => Pages\ListBulkRepayments::route('/'),
            'create' => Pages\CreateBulkRepayments::route('/create'),
            'view' => Pages\ViewBulkRepayments::route('/{record}'),
            'edit' => Pages\EditBulkRepayments::route('/{record}/edit'),
        ];
    }
}
