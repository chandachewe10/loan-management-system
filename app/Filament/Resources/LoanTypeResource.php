<?php

namespace App\Filament\Resources;

use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use App\Filament\Resources\LoanTypeResource\Pages;
use App\Filament\Resources\LoanTypeResource\RelationManagers;
use App\Models\LoanType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Exports\LoanTypeExporter;
use Filament\Tables\Actions\ExportAction;



class LoanTypeResource extends Resource
{
    protected static ?string $model = LoanType::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'Loan Agreement Forms';
    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('loan_name')
                    ->label('Loan Name')
                    ->prefixIcon('fas-dollar-sign')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('interest_rate')
                    ->label('Interest Rate')
                    ->prefixIcon('fas-percentage')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('interest_cycle')
                    ->label('Interest Cycle')
                    ->prefixIcon('fas-sync-alt')
                    ->options([
                        'day(s)' => 'Daily',
                        'week(s)' => 'Weekly',
                        'month(s)' => 'Monthly',
                        'year(s)' => 'Yearly',

                    ])
                    ->required(),


                Forms\Components\Radio::make('service_fee_type')
                    ->label('Select Service Fee Type')
                    ->helperText('Is your service fee a percentage or just a custom amount?')
                    ->options([
                        'service_fee_percentage' => "percentage",
                        'service_fee_custom_amount' => 'Custom Amount',
                        'none' => 'We dont Apply Service Fee',
                    ])
                    ->required()
                    ->live(),

                Forms\Components\TextInput::make('service_fee_percentage')
                    ->label('Enter Service/Processing fee')
                    ->numeric()
                    ->required()
                    ->visible(fn(Get $get) => $get('service_fee_type') === 'service_fee_percentage'),

                Forms\Components\TextInput::make('service_fee_custom_amount')
                    ->label('Enter Service/Processing fee amount')
                    ->numeric()
                    ->required()
                    ->visible(fn(Get $get) => $get('service_fee_type') === 'service_fee_custom_amount'),



                Forms\Components\Radio::make('penalty_fee_type')
                    ->label('Select Penalty Fee Type')
                    ->helperText('Is your penalty fee a percentage or just a custom amount?')
                    ->options([
                        'penalty_fee_percentage' => "percentage",
                        'penalty_fee_custom_amount' => 'Custom Amount',
                        'none' => 'We dont Apply Penalties',
                    ])
                    ->required()
                    ->live(),

                Forms\Components\TextInput::make('penalty_fee_percentage')
                    ->label('Enter Penalty fee')
                    ->numeric()
                    ->required()
                    ->visible(fn(Get $get) => $get('penalty_fee_type') === 'penalty_fee_percentage'),

                Forms\Components\TextInput::make('penalty_fee_custom_amount')
                    ->label('Enter penalty fee amount')
                    ->numeric()
                    ->required()
                    ->visible(fn(Get $get) => $get('penalty_fee_type') === 'penalty_fee_custom_amount'),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ExportAction::make()
                    ->exporter(LoanTypeExporter::class)
            ])
            ->columns([
                Tables\Columns\TextColumn::make('loan_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('interest_rate')
                    ->label('Interest Rate (%)')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('interest_cycle')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('service_fee')
                    ->label('Processing Fee')
                    ->badge()
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('interest_cycle')
                    ->options([
                        'day(s)' => 'Daily',
                        'week(s)' => 'Weekly',
                        'month(s)' => 'Monthly',
                        'year(s)' => 'Yearly',

                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListLoanTypes::route('/'),
            'create' => Pages\CreateLoanType::route('/create'),
            'view' => Pages\ViewLoanType::route('/{record}'),
            'edit' => Pages\EditLoanType::route('/{record}/edit'),
        ];
    }
}
