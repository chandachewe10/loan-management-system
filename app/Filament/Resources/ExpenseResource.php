<?php

namespace App\Filament\Resources;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use App\Filament\Resources\ExpenseResource\Pages;
use App\Filament\Resources\ExpenseResource\RelationManagers;
use Bavix\Wallet\Models\Wallet;
use App\Models\Expense;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationGroup = 'Expenses';
    protected static ?string $navigationIcon = 'fas-file';
    protected static ?int $navigationSort = 5;
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function form(Form $form): Form
    {
        $options = Wallet::all()->map(function ($wallet) {
            return [
                'value' => $wallet->id,
                'label' => $wallet->name . ' - Balance: ' . number_format($wallet->balance),
            ];
        });
        return $form->schema([Forms\Components\Select::make('category_id')->label('Expense Category')->prefixIcon('fas-copy')->relationship('expense_category', 'category_name')->searchable()->preload(), Forms\Components\Select::make('from_this_account')->label('From this Account')->prefixIcon('fas-wallet')->options($options->pluck('label', 'value')->toArray())->required()->searchable(), Forms\Components\TextInput::make('expense_name')->label('Expense Name')->prefixIcon('fas-file')->required()->maxLength(255), Forms\Components\TextInput::make('expense_amount')->label('Expense Amount')->prefixIcon('heroicon-o-user')->required()->numeric(), Forms\Components\DatePicker::make('expense_date')->label('Expense Date')->prefixIcon('heroicon-o-calendar')->live()->required()->native(false)->maxDate(now()), SpatieMediaLibraryFileUpload::make('expense_attachment')->disk('expenses')->visibility('public')->multiple()->minFiles(0)->maxFiles(10)->maxSize(5120)->columnSpan(2)->openable()]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([Tables\Columns\TextColumn::make('expense_category.category_name')->searchable(), Tables\Columns\TextColumn::make('expense_name')->searchable(), Tables\Columns\TextColumn::make('expense_amount')->badge()->searchable(), Tables\Columns\TextColumn::make('expense_date')->searchable()])
            // ->filters([
            //     Tables\Filters\SelectFilter::make('gender')
            //         ->options([
            //             'male' => 'Male',
            //             'female' => 'Female',
            //         ]),
            // ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make(), ExportBulkAction::make()])])
            ->emptyStateActions([Tables\Actions\CreateAction::make()]);
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
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'view' => Pages\ViewExpense::route('/{record}'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}
