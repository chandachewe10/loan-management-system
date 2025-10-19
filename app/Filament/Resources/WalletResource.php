<?php

namespace App\Filament\Resources;

use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use App\Filament\Resources\WalletResource\Pages;
use App\Filament\Resources\WalletResource\RelationManagers;
use App\Models\Wallet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Exports\WalletExporter;
use Filament\Tables\Actions\ExportAction;

class WalletResource extends Resource
{
    protected static ?string $model = Wallet::class;

    protected static ?string $navigationIcon = 'fas-wallet';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?int $navigationSort = 1;


    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Wallet Name - Account Name')
                    ->prefixIcon('fas-wallet')
                    ->required(),
                Forms\Components\Select::make('meta.currency')
                    ->label('Currency')
                    ->options([
                        'ZMW' => 'ZMW - Zambian Kwacha',
                    ])
                    ->default('ZMW')
                    ->required()
                    ->prefixIcon('fas-money-bill'),
                Forms\Components\TextInput::make('balance')
                    ->label('Current Balance')
                    ->placeholder(0.00)
                    ->readonly()
                    ->numeric()
                    ->prefixIcon('fas-dollar-sign'),
                Forms\Components\TextInput::make('amount')
                    ->label('Add Funds')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->prefixIcon('fas-dollar-sign'),



                Forms\Components\RichEditor::make('description')
                    ->label('Description')
                    ->columnSpan(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ExportAction::make()
                    ->exporter(WalletExporter::class)
            ])
            ->recordUrl(null)
            ->columns([

                Tables\Columns\TextColumn::make('name')
                    ->badge()

                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->badge()

                    ->searchable(),

                Tables\Columns\TextColumn::make('balance')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('meta')
                    ->label('Currency')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //  Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListWallets::route('/'),
            'create' => Pages\CreateWallet::route('/create'),
            'view' => Pages\ViewWallet::route('/{record}'),
            'edit' => Pages\EditWallet::route('/{record}/edit'),
        ];
    }
}
