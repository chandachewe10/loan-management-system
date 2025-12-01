<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DirectDebitMandateSettingsResource\Pages;
use App\Models\DirectDebitMandateSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DirectDebitMandateSettingsResource extends Resource
{
    protected static ?string $model = DirectDebitMandateSettings::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Loan Agreement Forms';
    protected static ?string $navigationLabel = 'Direct Debit Mandate Settings';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('service_provider_reference_number')
                    ->label('Service Provider Reference Number')
                    ->prefixIcon('heroicon-o-hashtag')
                    ->required()
                    ->helperText('The reference number assigned to your company (e.g., 001497001)')
                    ->maxLength(255),

                Forms\Components\TextInput::make('days_before_payment_date')
                    ->label('Days Before Payment Date')
                    ->prefixIcon('heroicon-o-calendar-days')
                    ->numeric()
                    ->required()
                    ->default(5)
                    ->helperText('How many days can the Direct Debit be processed before Payment Date?')
                    ->minValue(0)
                    ->maxValue(30),

                Forms\Components\TextInput::make('days_after_payment_date')
                    ->label('Days After Payment Date')
                    ->prefixIcon('heroicon-o-calendar-days')
                    ->numeric()
                    ->required()
                    ->default(5)
                    ->helperText('How many days can the Direct Debit be processed after Payment Date?')
                    ->minValue(0)
                    ->maxValue(30),

                Forms\Components\Select::make('default_payment_frequency')
                    ->label('Default Payment Frequency')
                    ->prefixIcon('heroicon-o-clock')
                    ->required()
                    ->default('M')
                    ->options([
                        'D' => 'Daily (D)',
                        'W' => 'Weekly (W)',
                        'FN' => 'Fortnightly (FN)',
                        'M' => 'Monthly (M)',
                        'Q' => 'Quarterly (Q)',
                        'H' => 'Half Yearly (H)',
                        'A' => 'Annually (A)',
                    ])
                    ->helperText('Default payment frequency to pre-select in the mandate form'),

                Forms\Components\Select::make('payment_date_calculation')
                    ->label('Payment Date Calculation')
                    ->prefixIcon('heroicon-o-calculator')
                    ->required()
                    ->default('loan_release_date')
                    ->options([
                        'loan_release_date' => 'Loan Release Date',
                        'loan_due_date' => 'Loan Due Date',
                    ])
                    ->helperText('Which date should be used as the Payment Date in the mandate?'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('service_provider_reference_number')
                    ->label('Reference Number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('days_before_payment_date')
                    ->label('Days Before')
                    ->badge(),
                Tables\Columns\TextColumn::make('days_after_payment_date')
                    ->label('Days After')
                    ->badge(),
                Tables\Columns\TextColumn::make('default_payment_frequency')
                    ->label('Payment Frequency')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'D' => 'Daily',
                        'W' => 'Weekly',
                        'FN' => 'Fortnightly',
                        'M' => 'Monthly',
                        'Q' => 'Quarterly',
                        'H' => 'Half Yearly',
                        'A' => 'Annually',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListDirectDebitMandateSettings::route('/'),
            'create' => Pages\CreateDirectDebitMandateSettings::route('/create'),
            'view' => Pages\ViewDirectDebitMandateSettings::route('/{record}'),
            'edit' => Pages\EditDirectDebitMandateSettings::route('/{record}/edit'),
        ];
    }
}

