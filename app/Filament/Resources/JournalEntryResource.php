<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JournalEntryResource\Pages;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Services\DoubleEntryService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class JournalEntryResource extends Resource
{
    protected static ?string $model = JournalEntry::class;

    protected static ?string $navigationGroup = 'Accounting';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Journal Entries';
    protected static ?string $modelLabel = 'Journal Entry';
    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Journal Entry Header')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('entry_number')
                            ->label('Entry Number')
                            ->placeholder('Auto-generated on save')
                            ->readOnly()
                            ->maxLength(30),

                        Forms\Components\DatePicker::make('entry_date')
                            ->label('Entry Date')
                            ->required()
                            ->default(now())
                            ->native(false),

                        Forms\Components\Select::make('source_type')
                            ->label('Source Type')
                            ->options(JournalEntry::SOURCE_TYPES)
                            ->default('manual')
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'posted' => 'Posted',
                                'voided' => 'Voided',
                            ])
                            ->default('posted')
                            ->required(),

                        Forms\Components\TextInput::make('description')
                            ->label('Description')
                            ->required()
                            ->columnSpan(2)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('reference')
                            ->label('Reference')
                            ->nullable()
                            ->maxLength(100),
                    ]),

                Forms\Components\Section::make('Journal Entry Lines')
                    ->description('Every entry must balance: Total Debits = Total Credits')
                    ->schema([
                        Forms\Components\Repeater::make('lines')
                            ->relationship()
                            ->label('')
                            ->columns(4)
                            ->schema([
                                Forms\Components\Select::make('account_id')
                                    ->label('Account')
                                    ->options(
                                        Account::withoutGlobalScopes()
                                            ->where('is_active', true)
                                            ->orderBy('code')
                                            ->get()
                                            ->mapWithKeys(fn($a) => [$a->id => "[{$a->code}] {$a->name}"])
                                    )
                                    ->searchable()
                                    ->required()
                                    ->columnSpan(2),

                                Forms\Components\Select::make('type')
                                    ->label('Type')
                                    ->options([
                                        'debit' => 'Debit (DR)',
                                        'credit' => 'Credit (CR)',
                                    ])
                                    ->required(),

                                Forms\Components\TextInput::make('amount')
                                    ->label('Amount')
                                    ->numeric()
                                    ->minValue(0.01)
                                    ->required()
                                    ->prefix('ZMW'),

                                Forms\Components\TextInput::make('description')
                                    ->label('Line Description')
                                    ->nullable()
                                    ->columnSpan(4),
                            ])
                            ->minItems(2)
                            ->addActionLabel('Add Line')
                            ->reorderable()
                            ->defaultItems(2),
                    ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Journal Entry')
                    ->columns(3)
                    ->schema([
                        Infolists\Components\TextEntry::make('entry_number')
                            ->label('Entry #')
                            ->badge()
                            ->color('primary'),

                        Infolists\Components\TextEntry::make('entry_date')
                            ->label('Date')
                            ->date('d M Y'),

                        Infolists\Components\TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn($state) => match ($state) {
                                'posted' => 'success',
                                'draft' => 'warning',
                                'voided' => 'danger',
                                default => 'gray',
                            }),

                        Infolists\Components\TextEntry::make('source_type')
                            ->label('Source')
                            ->badge()
                            ->formatStateUsing(fn($state) => JournalEntry::SOURCE_TYPES[$state] ?? ucfirst($state))
                            ->color('info'),

                        Infolists\Components\TextEntry::make('reference')
                            ->label('Reference'),

                        Infolists\Components\TextEntry::make('description')
                            ->label('Description')
                            ->columnSpan(3),
                    ]),

                Infolists\Components\Section::make('Ledger Lines')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('lines')
                            ->label('')
                            ->columns(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('account.code')
                                    ->label('Code')
                                    ->badge()
                                    ->fontFamily('mono'),

                                Infolists\Components\TextEntry::make('account.name')
                                    ->label('Account'),

                                Infolists\Components\TextEntry::make('type')
                                    ->label('DR / CR')
                                    ->badge()
                                    ->color(fn($state) => $state === 'debit' ? 'info' : 'success')
                                    ->formatStateUsing(fn($state) => strtoupper($state)),

                                Infolists\Components\TextEntry::make('amount')
                                    ->label('Amount')
                                    ->money('ZMW'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Totals')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('total_debits')
                            ->label('Total Debits')
                            ->getStateUsing(fn(JournalEntry $record) => 'ZMW ' . number_format($record->total_debits, 2))
                            ->badge()
                            ->color('info'),

                        Infolists\Components\TextEntry::make('total_credits')
                            ->label('Total Credits')
                            ->getStateUsing(fn(JournalEntry $record) => 'ZMW ' . number_format($record->total_credits, 2))
                            ->badge()
                            ->color('success'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('entry_date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('entry_number')
                    ->label('Entry #')
                    ->searchable()
                    ->badge()
                    ->color('primary')
                    ->fontFamily('mono'),

                Tables\Columns\TextColumn::make('entry_date')
                    ->label('Date')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('source_type')
                    ->label('Source')
                    ->badge()
                    ->formatStateUsing(fn($state) => JournalEntry::SOURCE_TYPES[$state] ?? ucfirst($state))
                    ->color(fn($state) => match ($state) {
                        'loan_disbursement' => 'info',
                        'loan_repayment' => 'success',
                        'expense' => 'danger',
                        'payroll' => 'warning',
                        'manual' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'posted' => 'success',
                        'draft' => 'warning',
                        'voided' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('total_debits')
                    ->label('Debits')
                    ->getStateUsing(fn(JournalEntry $record) => number_format($record->total_debits, 2))
                    ->alignRight()
                    ->fontFamily('mono'),

                Tables\Columns\TextColumn::make('total_credits')
                    ->label('Credits')
                    ->getStateUsing(fn(JournalEntry $record) => number_format($record->total_credits, 2))
                    ->alignRight()
                    ->fontFamily('mono'),

                Tables\Columns\TextColumn::make('reference')
                    ->label('Ref')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('source_type')
                    ->label('Source Type')
                    ->options(JournalEntry::SOURCE_TYPES),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'posted' => 'Posted',
                        'voided' => 'Voided',
                    ]),

                Tables\Filters\Filter::make('entry_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From Date')
                            ->native(false),
                        Forms\Components\DatePicker::make('until')
                            ->label('To Date')
                            ->native(false),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn($q, $d) => $q->whereDate('entry_date', '>=', $d))
                            ->when($data['until'], fn($q, $d) => $q->whereDate('entry_date', '<=', $d));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn(JournalEntry $record) => $record->source_type === 'manual' && $record->status === 'draft'),
                Tables\Actions\Action::make('void')
                    ->label('Void')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn(JournalEntry $record) => $record->status === 'posted')
                    ->action(function (JournalEntry $record) {
                        $record->update(['status' => 'voided']);

                        // Reverse any associated wallet balances
                        foreach ($record->lines as $line) {
                            $wallet = \App\Models\Wallet::withoutGlobalScopes()->where('account_id', $line->account_id)->first();
                            if ($wallet) {
                                $amount = (float) $line->amount;
                                if ($line->type === 'debit') {
                                    $wallet->withdraw($amount, ['meta' => 'Reversal of voided entry: ' . $record->entry_number]);
                                } else {
                                    $wallet->deposit($amount, ['meta' => 'Reversal of voided entry: ' . $record->entry_number]);
                                }
                            }
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('New Manual Entry'),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJournalEntries::route('/'),
            'create' => Pages\CreateJournalEntry::route('/create'),
            'view' => Pages\ViewJournalEntry::route('/{record}'),
            'edit' => Pages\EditJournalEntry::route('/{record}/edit'),
        ];
    }
}
