<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BranchesResource\Pages;
use App\Filament\Resources\BranchesResource\RelationManagers;
use App\Models\Branches;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Exports\BranchesExporter;
use Filament\Tables\Actions\ExportAction;

class BranchesResource extends Resource
{
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    protected static ?string $model = Branches::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'Branches';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('branch_name')
                    ->label('Branch Name')
                    ->unique()
                    ->prefixIcon('heroicon-o-building-storefront')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('street')
                    ->label('Street')
                    ->prefixIcon('heroicon-o-arrow-path')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('address')
                    ->label('Address')
                    ->prefixIcon('heroicon-o-home')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('province')
                    ->label('Province')
                    ->prefixIcon('heroicon-o-home-modern')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('city')
                    ->label('City')
                    ->prefixIcon('heroicon-o-home-modern')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('mobile')
                    ->label('Branch Phone number')
                    ->prefixIcon('heroicon-o-phone')
                    ->tel()
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->label('Branch Email address')
                    ->prefixIcon('heroicon-o-envelope')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('branch_manager')
                    ->prefixIcon('heroicon-o-user-circle')
                    ->label('Branch Manager')
                    ->preload()
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required()

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
                ->headerActions([
            ExportAction::make()
                ->exporter(BranchesExporter::class),
        ])
            ->columns([
                Tables\Columns\TextColumn::make('branch_name')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('street')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),

                Tables\Columns\TextColumn::make('province')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mobile')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Branch Manager')
                    ->badge()
                    ->searchable(),

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
            'index' => Pages\ListBranches::route('/'),
            'create' => Pages\CreateBranches::route('/create'),
            'view' => Pages\ViewBranches::route('/{record}'),
            'edit' => Pages\EditBranches::route('/{record}/edit'),
        ];
    }
}
