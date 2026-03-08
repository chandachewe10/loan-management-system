<?php

namespace App\Filament\Resources;

use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use App\Filament\Resources\WalletResource\Pages;
use App\Filament\Resources\WalletResource\RelationManagers;
use App\Models\Account;
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
    protected static ?string $navigationLabel = 'Bank / Cash Accounts';
    protected static ?string $modelLabel = 'Bank / Cash Account';
    protected static ?int $navigationSort = 0;


    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Account Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Account Name')
                            ->prefixIcon('fas-wallet')
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\Select::make('meta.currency')
                            ->label('Currency')
                            ->options([
                                'ZMW' => 'ZMW - Zambian Kwacha',
                            ])
                            ->default('ZMW')
                            ->required()
                            ->prefixIcon('fas-money-bill')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('balance')
                            ->label('Wallet Balance')
                            ->default(0)
                            ->numeric()
                            ->prefixIcon('fas-dollar-sign')
                            ->columnSpan(1)
                            ->helperText('Setting this will adjust the balance.'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Chart of Accounts Link')
                    ->description('Link this account to the Chart of Accounts for double-entry accounting. If left empty, a sub-account will be created automatically.')
                    ->schema([
                        Forms\Components\Select::make('account_id')
                            ->label('Linked Chart of Accounts Entry')
                            ->options(
                                Account::withoutGlobalScopes()
                                    ->where('is_active', true)
                                    ->where('type', 'asset')
                                    ->orderBy('code')
                                    ->get()
                                    ->mapWithKeys(fn($a) => [$a->id => "[{$a->code}] {$a->name}"])
                            )
                            ->searchable()
                            ->nullable()
                            ->placeholder('Auto-create sub-account under 1010 Cash & Bank')
                            ->helperText('Choose an existing asset account, or leave blank to auto-create one.')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\RichEditor::make('description')
                    ->label('Description')
                    ->columnSpan(2),
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
                    ->label('Account Name')
                    ->badge()
                    ->searchable(),

                Tables\Columns\TextColumn::make('balance')
                    ->label('Wallet Balance')
                    ->badge()
                    ->color(fn($state) => $state > 0 ? 'success' : 'danger')
                    ->money('ZMW')
                    ->searchable(),

                Tables\Columns\TextColumn::make('account.code')
                    ->label('Acct Code')
                    ->badge()
                    ->color('gray')
                    ->placeholder('Not linked')
                    ->searchable(),

                Tables\Columns\TextColumn::make('account.name')
                    ->label('Linked Account')
                    ->placeholder('Not linked')
                    ->searchable(),

                Tables\Columns\TextColumn::make('meta')
                    ->label('Currency')
                    ->badge()
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->limit(40)
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->date()
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
