<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaxBandResource\Pages;
use App\Models\TaxBand;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TaxBandResource extends Resource
{
    protected static ?string $model = TaxBand::class;

    protected static ?string $navigationIcon = 'heroicon-o-scale';
    protected static ?string $navigationLabel = 'Tax Bands';
    protected static ?string $navigationGroup = 'Payroll';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Tax Band Name')
                    ->required()
                    ->maxLength(255)
                    ->prefixIcon('heroicon-o-tag')
                    ->helperText('e.g., "First Band", "Second Band", etc.'),

                Forms\Components\TextInput::make('min_income')
                    ->label('Minimum Income (ZMW)')
                    ->required()
                    ->numeric()
                    ->prefixIcon('heroicon-o-currency-dollar')
                    ->helperText('Minimum income threshold for this tax band'),

                Forms\Components\TextInput::make('max_income')
                    ->label('Maximum Income (ZMW)')
                    ->numeric()
                    ->prefixIcon('heroicon-o-currency-dollar')
                    ->helperText('Maximum income threshold (leave empty for unlimited)')
                    ->nullable(),

                Forms\Components\TextInput::make('tax_rate')
                    ->label('Tax Rate (%)')
                    ->required()
                    ->numeric()
                    ->prefixIcon('heroicon-o-percent-badge')
                    ->minValue(0)
                    ->maxValue(100)
                    ->step(0.01)
                    ->helperText('Tax rate percentage for this band'),

                Forms\Components\TextInput::make('fixed_amount')
                    ->label('Fixed Tax Amount (ZMW)')
                    ->numeric()
                    ->prefixIcon('heroicon-o-currency-dollar')
                    ->default(0)
                    ->helperText('Fixed tax amount (if applicable)'),

                Forms\Components\TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->helperText('Order in which this band should be applied (lower numbers first)'),

                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->helperText('Only active tax bands will be used in calculations'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('min_income')
                    ->label('Min Income')
                    ->money('ZMW')
                    ->sortable(),

                Tables\Columns\TextColumn::make('max_income')
                    ->label('Max Income')
                    ->money('ZMW')
                    ->sortable()
                    ->default('Unlimited'),

                Tables\Columns\TextColumn::make('tax_rate')
                    ->label('Tax Rate')
                    ->formatStateUsing(fn ($state) => $state . '%')
                    ->sortable(),

                Tables\Columns\TextColumn::make('fixed_amount')
                    ->label('Fixed Amount')
                    ->money('ZMW')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTaxBands::route('/'),
        ];
    }
}

