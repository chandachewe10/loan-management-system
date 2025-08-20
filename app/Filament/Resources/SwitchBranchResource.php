<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SwitchBranchResource\Pages;
use App\Filament\Resources\SwitchBranchResource\RelationManagers;
use App\Models\User as SwitchBranch;
use App\Models\Branches;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SwitchBranchResource extends Resource
{
    protected static ?string $model = SwitchBranch::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $navigationGroup = 'Branches';
    protected static ?string $recordTitleAttribute = 'Switch Branches';
    protected static ?string $modelLabel = 'Switch Branch';
    protected static ?string $pluralModelLabel = 'Switch Branches';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        $branchName = Branches::find(auth()->user()->branch_id)->branch_name ?? 'Main Branch';
        return $form
            ->schema([
                Forms\Components\Select::make('branch_id')
                    ->label('Switch to Branch')
                    ->options(function () {
                        $branches = Branches::orderBy('branch_name')->where('organization_id', auth()->user()->organization_id)->pluck('branch_name', 'id');
                        return [0 => 'Main Branch'] + $branches->toArray();
                    })
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('current_branch')
                    ->label('Current Branch')
                    ->default($branchName)
                    ->disabled()


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //  Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // Add this method to override the default navigation URL
    public static function getNavigationUrl(): string
    {
        return static::getUrl('create');
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
            'index' => Pages\ListSwitchBranches::route('/'),
            'create' => Pages\CreateSwitchBranch::route('/create'),
            'view' => Pages\ViewSwitchBranch::route('/{record}'),
            'edit' => Pages\EditSwitchBranch::route('/{record}/edit'),
        ];
    }
}
