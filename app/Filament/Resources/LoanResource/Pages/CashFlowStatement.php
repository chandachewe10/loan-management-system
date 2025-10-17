<?php

namespace App\Filament\Resources\LoanResource\Pages;

use App\Filament\Resources\LoanResource;
use App\Models\User;
use Bavix\Wallet\Models\Transaction;
use Bavix\Wallet\Models\Wallet;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CashFlowStatement extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = LoanResource::class;
    protected static string $view = 'filament.resources.loan-resource.pages.cash-flow-statement';
    protected static ?string $navigationLabel = 'Cash Flow Statement';
    protected static ?string $title = 'Cash Flow Statement';

    public $wallets;
    public $totalBalance;
    public $consolidatedData;

    

    public static function getNavigationBadge(): ?string
    {
        $user = auth()->user();
        $wallets = Wallet::where('holder_id', $user->id)->get();
        return 'ZMW ' . number_format($wallets->sum('balance'), 2);
    }

    public function mount(): void
    {
        $user = auth()->user();

        // Get all wallets
        $this->wallets = Wallet::where('holder_id', $user->id)->get();
        $this->totalBalance = $this->wallets->sum('balance');
       
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTransactionQuery())
            ->columns([
                TextColumn::make('wallet.name')
                    ->label('Account')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn($state) => ucfirst($state))
                    ->color(fn(string $state): string => match ($state) {
                        'deposit' => 'success',
                        'withdraw' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(
                        fn($state, Transaction $record) =>
                        'ZMW ' . number_format(abs($state), 2)
                    )
                    ->color(fn(Transaction $record) => $record->amount > 0 ? 'success' : 'danger'),

                TextColumn::make('wallet.meta')
                    ->label('Currency')

                    ->badge()
,

                TextColumn::make('wallet.description')
                    ->label('Description')
                    ->limit(50),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('wallet')
                    ->relationship('wallet', 'name')
                    ->label('Account'),

                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'operating' => 'Operating Activities',
                        'investing' => 'Investing Activities',
                        'financing' => 'Financing Activities',
                    ]),
            ])
            ->actions([
                
            ])
            ->bulkActions([
                
            ]);
    }

    protected function getTransactionQuery(): Builder
    {
        $user = auth()->user();

        return Transaction::query()
            ->where('payable_id', $user->id)
            ->where('payable_type', User::class)
            ->with('wallet')
            ->whereBetween('created_at', [
                now()->startOfMonth(),
                now()->endOfMonth()
            ]);
    }



    
}
