<?php

namespace App\Filament\Resources;
use Filament\Forms\Components\Toggle;
use App\Filament\Resources\ThirdPartyResource\Pages;
use App\Filament\Resources\ThirdPartyResource\RelationManagers;
use App\Models\ThirdParty;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ThirdPartyResource extends Resource
{
    protected static ?string $model = ThirdParty::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Third Party Name')
                    ->prefixIcon('heroicon-o-user')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('base_uri')
                    ->prefix('https://')
                    ->suffixIcon('heroicon-m-globe-alt'),
                Forms\Components\TextInput::make('endpoint')
                    ->suffixIcon('heroicon-m-globe-alt')
                    ->prefix('/'),
                    Forms\Components\TextInput::make('token')
                    ->label('API/TOKEN')
                    ->prefixIcon('fas-lock'),
                    Forms\Components\TextInput::make('sender_id')
                    ->label('Sender ID')
                    ->minLength(2)
                    ->maxLength(12),
                    Toggle::make('is_active')
                    ->onColor('success')
                    ->offColor('danger')

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListThirdParties::route('/'),
            'create' => Pages\CreateThirdParty::route('/create'),
            'view' => Pages\ViewThirdParty::route('/{record}'),
            'edit' => Pages\EditThirdParty::route('/{record}/edit'),
        ];
    }
}
