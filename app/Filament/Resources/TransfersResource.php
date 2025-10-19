<?php

namespace App\Filament\Resources;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Filament\Forms\Components\Select;
use App\Models\Wallet;
use App\Models\Transfer;
use App\Filament\Resources\TransfersResource\Pages;
use App\Filament\Resources\TransfersResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Exports\TransferExporter;
use Filament\Tables\Actions\ExportAction;
use Auth;

class TransfersResource extends Resource
{
    protected static ?string $model = Wallet::class;

    protected static ?string $navigationIcon = 'fas-wallet';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?string $navigationLabel = 'Transfers';


    protected static ?int $navigationSort = 6;

    public static function getNavigationBadge(): ?string
    {
        return Transfer::count();
    }


    public static function form(Form $form): Form
    {
        $options = Wallet::all()->map(function ($wallet) {
            return [
                'value' => $wallet->id,
                'label' => $wallet->name . ' - Balance: ' . number_format($wallet->balance)
            ];
        });
        return $form
            ->schema([

                Select::make('from_this_account')
                    ->label('From this Account')
                    ->prefixIcon('fas-wallet')
                    ->options($options->pluck('label', 'value')->toArray())
                    ->required()
                    ->searchable(),
                Select::make('to_this_account')
                    ->label('To this Account')
                    ->prefixIcon('fas-wallet')
                    ->options($options->pluck('label', 'value')->toArray())
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('amount_to_transfer')
                    ->label('Amount to Transfer')
                    ->required()
                    ->numeric()
                    ->prefixIcon('fas-dollar-sign'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
      ->modifyQueryUsing(function ($query) {
            $query->whereHas('from', function ($q) {
                $q->where('organization_id', Auth::user()->organization_id);
            });
        })

            ->columns([
                Tables\Columns\TextColumn::make('from.name')
                    ->badge()
                    ->searchable(),
                    //  Tables\Columns\TextColumn::make('from.organization_id')
                    // ->badge()
                    // ->searchable(), this can show an organization_id how can I fiter using auth user->or_id
                Tables\Columns\TextColumn::make('to.name')
                    ->badge()
                    ->searchable(),

                Tables\Columns\TextColumn::make('deposit.amount')
                    ->money('ZMW')
                    ->badge()
                    ->searchable()

            ])
            ->filters([
                //
            ])
            ->actions([])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('New Transfer'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return Transfer::where('status', 'transfer');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransfers::route('/'),
            'create' => Pages\CreateTransfers::route('/create'),
            'view' => Pages\ViewTransfers::route('/{record}'),
            'edit' => Pages\EditTransfers::route('/{record}/edit'),
        ];
    }
}
