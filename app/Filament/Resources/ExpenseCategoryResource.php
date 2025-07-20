<?php

namespace App\Filament\Resources;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use App\Filament\Resources\ExpenseCategoryResource\Pages;
use App\Filament\Resources\ExpenseCategoryResource\RelationManagers;
use App\Models\ExpenseCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Exports\ExpenseCategoryExporter;
use Filament\Tables\Actions\ExportAction;

class ExpenseCategoryResource extends Resource
{
    protected static ?string $model = ExpenseCategory::class;

    protected static ?string $navigationGroup = 'Expenses';
    protected static ?string $navigationIcon = 'fas-dollar-sign'; 
    protected static ?int $navigationSort = 4;
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
      public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('category_name')
                ->label('Category Name')
                ->prefixIcon('heroicon-o-user')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
                Forms\Components\TextInput::make('category_code')
                ->label('Category Code')
                ->prefixIcon('heroicon-o-user')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
          ->headerActions([
            ExportAction::make()
                ->exporter(ExpenseCategoryExporter::class)
        ])
            ->columns([
                Tables\Columns\TextColumn::make('category_name')
                ->searchable(),
            Tables\Columns\TextColumn::make('category_code')
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
                Tables\Actions\DeleteBulkAction::make()
                ,
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
            'index' => Pages\ListExpenseCategories::route('/'),
            'create' => Pages\CreateExpenseCategory::route('/create'),
            'view' => Pages\ViewExpenseCategory::route('/{record}'),
            'edit' => Pages\EditExpenseCategory::route('/{record}/edit'),
        ];
    }    
}
