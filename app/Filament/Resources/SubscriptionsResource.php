<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionsResource\Pages;
use App\Filament\Resources\SubscriptionsResource\RelationManagers;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Support\HtmlString;
use App\Models\Payments as Subscriptions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Placeholder;

class SubscriptionsResource extends Resource
{
    protected static ?string $model = Subscriptions::class;
    protected static ?string $recordTitleAttribute = 'Subscriptions';
    protected static ?string $modelLabel = 'Subscriptions';
    protected static ?string $pluralModelLabel = 'Subscriptions';

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static bool $shouldRegisterNavigation = false;
    public static function form(Form $form): Form
    {
        return $form->schema([
            Grid::make([
                'default' => 1,
                'md' => 2,
                'lg' => 3, // 3 cards per row on large screens
            ])
                ->schema([
                    Card::make([
                        Placeholder::make('$45/m')
                            ->label(new HtmlString('<h2 class="text-xl font-bold">$45/m</h2>'))

                            ->content(new HtmlString('
                    <hr class="my-2 border-gray-300">
            <ul class="list-none pl-5 space-y-1">
                <li>✔ 1 User</li>
                <li>✔ 1 Branch </li>
                <li>✔ Unlimited Borrowers</li>
                <li>✔ 500 Messages</li>
                <li>✔ 1000 Loans Max</li>
                <li>✔ All other features included</li>
            </ul>
        ')),
                    ])->footerActions([
                        Action::make('subscribe20')
                            ->label('Subscribe')
                            ->button()
                            ->color('success')
                            ->url(fn() => route('subscription.lenco', ['amount' => encrypt(990)]))
                    ])->columnSpan(1),

                    Card::make([
                        Placeholder::make('$60/m')
                            ->label(new HtmlString('<h2 class="text-xl font-bold">$60/m</h2>'))

                            ->content(new HtmlString('
                    <hr class="my-2 border-gray-300">
            <ul class="list-none pl-5 space-y-1">
                <li>✔ 2 Users</li>
                <li>✔ 2 Branches </li>
                <li>✔ Unlimited Borrowers</li>
                <li>✔ 1000 Messages</li>
                <li>✔ 10,000 Loans Max</li>
                <li>✔ All other features included</li>
            </ul>
        ')),
                    ])->footerActions([
                        Action::make('subscribe20')
                            ->label('Subscribe')
                            ->button()
                            ->color('success')
                            ->url(fn() => route('subscription.lenco', ['amount' => encrypt(1320)]))
                    ])->columnSpan(1),


                    Card::make([
                        Placeholder::make('$120/m')
                            ->label(new HtmlString('<h2 class="text-xl font-bold">$120/m</h2>'))

                            ->content(new HtmlString('
                    <hr class="my-2 border-gray-300">
            <ul class="list-none pl-5 space-y-1">
                <li>✔ Unlimited Users</li>
                <li>✔ Unlimited Branches</li>
                <li>✔ Unlimited Borrowers</li>
                <li>✔ Unlimited Messages</li>
                <li>✔ Unlimited Loans</li>
                <li>✔ All other features included</li>
            </ul>
        ')),
                    ])->footerActions([
                        Action::make('subscribe20')
                            ->label('Subscribe')
                            ->button()
                            ->color('success')
                            ->url(fn() => route('subscription.lenco', ['amount' => encrypt(2640)]))
                    ])->columnSpan(1),

                ]),
        ]);
    }


    public static function table(Table $table): Table
    {
        return $table

            ->columns([
                Tables\Columns\TextColumn::make('payment_made_at')
                    ->date('j F Y')
                    ->badge('success')
                    ->searchable(),

                Tables\Columns\TextColumn::make('transaction_reference')
                    ->searchable(),

                Tables\Columns\TextColumn::make('payment_amount')
                    ->searchable(),

                Tables\Columns\TextColumn::make('payment_expires_at')
                    ->date('j F Y')
                    ->badge()
                    ->color(fn ($record) =>
        \Carbon\Carbon::parse($record->payment_expires_at)->isPast() ? 'danger' : 'success'
    )
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscriptions::route('/create'),
            'view' => Pages\ViewSubscriptions::route('/{record}'),
            'edit' => Pages\EditSubscriptions::route('/{record}/edit'),
        ];
    }
}
