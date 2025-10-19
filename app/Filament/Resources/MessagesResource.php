<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MessagesResource\Pages;
use App\Filament\Resources\MessagesResource\RelationManagers;
use App\Models\Messages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Exports\MessagesExporter;
use Filament\Tables\Actions\ExportAction;

class MessagesResource extends Resource
{
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    protected static ?string $model = Messages::class;
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';
    protected static ?string $modelLabel = 'Send SMSes';
    protected static ?string $navigationGroup = 'Addons';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Repeater::make('contact')
                    ->label('Phone Number(s)')
                    ->schema([
                        Forms\Components\TextInput::make('contact')
                            ->label('Phone')
                            ->prefixIcon('heroicon-o-phone')
                            ->required()
                            ->maxLength(255)
                            ->tel()
                            ->telRegex('/^(09|07)[5|6|7][0-9]{7}$/')
                    ])
                    ->columnSpan(2)
                    ->addActionLabel('Add Phone number'),


                Forms\Components\Textarea::make('message')
                ->helperText('Write in not more than 160 characters')
                    ->minLength(2)
                    ->maxLength(160)
                    ->rows(5)
                    ->columnSpan(2),



                Forms\Components\TextInput::make('status')
                    ->hidden()
                    ->maxLength(255),
            ]);
    }


public static function table(Table $table): Table
    {
        return $table
          ->headerActions([
            ExportAction::make()
                ->exporter(MessagesExporter::class)
        ])

            ->columns([
                Tables\Columns\TextColumn::make('index')
                ->label('No')
                ->rowIndex(),
                Tables\Columns\TextColumn::make('message')

                    ->searchable(),

                Tables\Columns\TextColumn::make('contact')
                ->label('Destination')
                ->badge()
                    ->searchable(),
                    Tables\Columns\TextColumn::make('created_at')
                ->label('Sent Date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('responseText')
                ->label('Message Status')
                    ->searchable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordUrl(null)
            ->recordAction(null)
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
                  //  ExportBulkAction::make()
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
            'index' => Pages\ListMessages::route('/'),
            'create' => Pages\CreateMessages::route('/create'),
            'view' => Pages\ViewMessages::route('/{record}'),
            'edit' => Pages\EditMessages::route('/{record}/edit'),
        ];
    }
}
