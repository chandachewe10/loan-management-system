<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LedgerEntryResource\Pages;
use App\Filament\Resources\LedgerEntryResource\RelationManagers;
use App\Models\LedgerEntry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Exports\LedgerEntryExporter;
use Filament\Tables\Actions\ExportAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LedgerEntryResource extends Resource
{
    protected static ?string $model = LedgerEntry::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?int $navigationSort = 1;

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
                    ->exporter(LedgerEntryExporter::class)
            ])
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->searchable(),

                Tables\Columns\TextColumn::make('wallet_type.name')
                    ->label('Account')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('debit')
                    ->label('Debit')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('credit')
                    ->label('Credit')
                    ->badge()
                    ->searchable(),

                Tables\Columns\TextColumn::make('transaction_type.uuid')
                    ->label('Transaction ID')
                    ->searchable(),
                    Tables\Columns\TextColumn::make('balance')
    ->label('Balance')
    ->getStateUsing(function ($record) {
        
        $debitTotal = LedgerEntry::where('wallet_id', $record->wallet_id)
            ->where('id', '<=', $record->id)
            ->sum('debit');

        $creditTotal = LedgerEntry::where('wallet_id', $record->wallet_id)
            ->where('id', '<=', $record->id)
            ->sum('credit');

        return $debitTotal - $creditTotal;
    })
    ->badge()
    ->sortable(),

            ])
            ->filters([
                //
            ])
            ->actions([
              

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([]),
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
            'index' => Pages\ListLedgerEntries::route('/'),
            'create' => Pages\CreateLedgerEntry::route('/create'),
            'view' => Pages\ViewLedgerEntry::route('/{record}'),
            'edit' => Pages\EditLedgerEntry::route('/{record}/edit'),
        ];
    }
}
