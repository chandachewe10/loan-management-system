<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccountResource\Pages;
use App\Models\Account;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Colors\Color;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static ?string $navigationGroup = 'Accounting';
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Chart of Accounts';
    protected static ?string $modelLabel = 'Account';
    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Account Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Account Code')
                            ->required()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true)
                            ->placeholder('e.g. 1010'),

                        Forms\Components\TextInput::make('name')
                            ->label('Account Name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('type')
                            ->label('Account Type')
                            ->required()
                            ->options(Account::TYPES)
                            ->reactive()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                // Auto-set normal balance based on account type
                                $set('normal_balance', match ($state) {
                                    'asset', 'expense' => 'debit',
                                    'liability', 'equity', 'revenue' => 'credit',
                                    default => null,
                                });
                            }),

                        Forms\Components\Select::make('normal_balance')
                            ->label('Normal Balance')
                            ->required()
                            ->options([
                                'debit' => 'Debit (Assets & Expenses)',
                                'credit' => 'Credit (Liabilities, Equity & Revenue)',
                            ]),

                        Forms\Components\Select::make('parent_id')
                            ->label('Parent Account')
                            ->relationship('parent', 'name')
                            ->getOptionLabelFromRecordUsing(fn(Account $record) => "[{$record->code}] {$record->name}")
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->nullable()
                            ->columnSpan(2),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),

                        Forms\Components\Toggle::make('is_system')
                            ->label('System Account (cannot be deleted)')
                            ->default(false)
                            ->disabled(fn($record) => $record?->is_system),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('code')
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->sortable()
                    ->searchable()
                    ->fontFamily('mono')
                    ->badge()
                    ->color(fn(Account $record) => match ($record->type) {
                        'asset' => 'info',
                        'liability' => 'warning',
                        'equity' => 'success',
                        'revenue' => 'primary',
                        'expense' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('name')
                    ->label('Account Name')
                    ->searchable()
                    ->sortable()
                    ->description(fn(Account $record) => $record->parent ? "↳ " . $record->parent->name : null),

                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn($state) => Account::TYPES[$state] ?? ucfirst($state))
                    ->color(fn(Account $record) => match ($record->type) {
                        'asset' => 'info',
                        'liability' => 'warning',
                        'equity' => 'success',
                        'revenue' => 'primary',
                        'expense' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('normal_balance')
                    ->label('Normal Balance')
                    ->badge()
                    ->color(fn($state) => $state === 'debit' ? 'info' : 'success')
                    ->formatStateUsing(fn($state) => ucfirst($state)),

                Tables\Columns\TextColumn::make('balance_display')
                    ->label('Balance')
                    ->getStateUsing(fn(Account $record) => number_format($record->getBalance(), 2))
                    ->alignRight()
                    ->fontFamily('mono'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_system')
                    ->label('System')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(Account::TYPES),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn(Account $record) => !$record->is_system),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn(Account $record) => !$record->is_system),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccounts::route('/'),
            'create' => Pages\CreateAccount::route('/create'),
            'view' => Pages\ViewAccount::route('/{record}'),
            'edit' => Pages\EditAccount::route('/{record}/edit'),
        ];
    }
}
