<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssetResource\Pages;
use App\Filament\Resources\AssetResource\RelationManagers;
use App\Models\Asset;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Exports\AssetExporter;
use Filament\Tables\Actions\ExportAction;

class AssetResource extends Resource
{
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    protected static ?string $model = Asset::class;

    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?int $navigationSort = 2;

    public static function getNavigationItems(): array
    {
        $items = parent::getNavigationItems();
        
        // Exclude statement pages from making Assets navigation active
        $excludedPaths = [
            'admin/assets/statement-of-financial-position',
            'admin/assets/statement-of-comprehensive-income',
        ];
        
        $currentPath = request()->path();
        
        if (in_array($currentPath, $excludedPaths)) {
            // Create new navigation items with custom active check that always returns false
            return array_map(function ($item) {
                // Clone the item and override isActiveWhen to return false
                $newItem = \Filament\Navigation\NavigationItem::make($item->getLabel())
                    ->url($item->getUrl())
                    ->icon($item->getIcon())
                    ->group($item->getGroup())
                    ->sort($item->getSort())
                    ->isActiveWhen(fn (): bool => false);
                
                // Add badge if it exists
                if ($badge = $item->getBadge()) {
                    $newItem->badge($badge);
                }
                
                return $newItem;
            }, $items);
        }
        
        return $items;
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('asset_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('asset_code')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('asset_category_id')
                    ->label('Asset Category')
                    ->required()
                    ->prefixIcon('fas-copy')
                    ->relationship('asset_category', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\DatePicker::make('purchase_date')
                    ->native(false)
                    ->required(),
                Forms\Components\TextInput::make('purchase_cost')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('supplier')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('useful_life_years')
                    ->required()
                    ->numeric()
                    ->default(5),
                Forms\Components\Select::make('depreciation_method')
                    ->label('Depreciation Method')
                    ->options([
                        'straight_line' => 'Straight Line',
                        'reducing_balance' => 'Reducing Balance',

                    ])
                    ->required(),

                Forms\Components\TextInput::make('depreciation_rate')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('accumulated_depreciation')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('net_book_value')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('location')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('custodian')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->prefixIcon('heroicon-o-users')
                    ->options([
                        'active' => 'Active',
                        'damaged' => 'Damaged',
                        'disposed' => 'Disposed',

                    ])
                    ->required(),
                Forms\Components\DatePicker::make('disposal_date')
                    ->native(false),
                Forms\Components\TextInput::make('disposal_value')
                    ->numeric()
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ExportAction::make()
                    ->exporter(AssetExporter::class),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('asset_name')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('asset_code')
                    ->unique()
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('asset_category.name')
                    ->badge()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('purchase_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('purchase_cost')
                    ->badge()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('supplier')
                    ->searchable(),
                Tables\Columns\TextColumn::make('useful_life_years')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('depreciation_method'),
                Tables\Columns\TextColumn::make('depreciation_rate')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('accumulated_depreciation')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('net_book_value')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('location')
                    ->searchable(),
                Tables\Columns\TextColumn::make('custodian')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()

                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'damaged' => 'warning',
                        'disposed' => 'danger',
                        default => 'info',
                    }),
                Tables\Columns\TextColumn::make('disposal_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('disposal_value')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'statement' => Pages\StatementOfFinancialPosition::route('/statement-of-financial-position'),
            'comprehensive_income' => Pages\StatementOfComprehensiveIncome::route('/statement-of-comprehensive-income'),
            'index' => Pages\ListAssets::route('/'),
            'create' => Pages\CreateAsset::route('/create'),
            'view' => Pages\ViewAsset::route('/{record}'),
            'edit' => Pages\EditAsset::route('/{record}/edit'),

        ];
    }
}
