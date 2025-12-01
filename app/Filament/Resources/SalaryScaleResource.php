<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalaryScaleResource\Pages;
use App\Models\SalaryScale;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SalaryScaleResource extends Resource
{
    protected static ?string $model = SalaryScale::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Salary Scales';
    protected static ?string $navigationGroup = 'Payroll';
    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Scale Information')
                    ->schema([
                        Forms\Components\TextInput::make('scale_name')
                            ->label('Scale Name')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-tag')
                            ->helperText('e.g., "Grade 1", "Manager Level", "Entry Level"'),

                        Forms\Components\TextInput::make('scale_code')
                            ->label('Scale Code')
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-hashtag')
                            ->helperText('Optional code for this scale (e.g., "G1", "MGR-1")'),

                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->maxLength(1000)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Order for displaying scales (lower numbers first)'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Only active scales can be assigned to employees'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Salary Structure')
                    ->description('Define the salary components for this scale')
                    ->schema([
                        Forms\Components\TextInput::make('basic_salary')
                            ->label('Basic Salary (ZMW)')
                            ->required()
                            ->numeric()
                            ->prefixIcon('heroicon-o-currency-dollar')
                            ->default(0)
                            ->live(onBlur: true),

                        Forms\Components\TextInput::make('housing_allowance')
                            ->label('Housing Allowance (ZMW)')
                            ->numeric()
                            ->default(0)
                            ->prefixIcon('heroicon-o-home')
                            ->live(onBlur: true),

                        Forms\Components\TextInput::make('transport_allowance')
                            ->label('Transport Allowance (ZMW)')
                            ->numeric()
                            ->default(0)
                            ->prefixIcon('heroicon-o-truck')
                            ->live(onBlur: true),

                        Forms\Components\TextInput::make('medical_allowance')
                            ->label('Medical Allowance (ZMW)')
                            ->numeric()
                            ->default(0)
                            ->prefixIcon('heroicon-o-heart')
                            ->live(onBlur: true),

                        Forms\Components\TextInput::make('other_allowances')
                            ->label('Other Allowances (ZMW)')
                            ->numeric()
                            ->default(0)
                            ->prefixIcon('heroicon-o-plus-circle')
                            ->live(onBlur: true),

                        Forms\Components\Placeholder::make('total_allowances')
                            ->label('Total Allowances')
                            ->content(fn (Forms\Get $get) => number_format(
                                (float)($get('housing_allowance') ?? 0) +
                                (float)($get('transport_allowance') ?? 0) +
                                (float)($get('medical_allowance') ?? 0) +
                                (float)($get('other_allowances') ?? 0),
                                2
                            ) . ' ZMW')
                            ->extraAttributes(['class' => 'font-semibold text-blue-600']),

                        Forms\Components\Placeholder::make('gross_salary')
                            ->label('Gross Salary')
                            ->content(fn (Forms\Get $get) => number_format(
                                (float)($get('basic_salary') ?? 0) +
                                (float)($get('housing_allowance') ?? 0) +
                                (float)($get('transport_allowance') ?? 0) +
                                (float)($get('medical_allowance') ?? 0) +
                                (float)($get('other_allowances') ?? 0),
                                2
                            ) . ' ZMW')
                            ->extraAttributes(['class' => 'font-bold text-lg text-green-600']),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('scale_name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('scale_code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->badge(),

                Tables\Columns\TextColumn::make('basic_salary')
                    ->label('Basic Salary')
                    ->money('ZMW')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_allowances')
                    ->label('Total Allowances')
                    ->money('ZMW')
                    ->getStateUsing(fn (SalaryScale $record) => $record->total_allowances)
                    ->sortable(),

                Tables\Columns\TextColumn::make('gross_salary')
                    ->label('Gross Salary')
                    ->money('ZMW')
                    ->getStateUsing(fn (SalaryScale $record) => $record->gross_salary)
                    ->sortable()
                    ->color('success')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('employees_count')
                    ->label('Employees')
                    ->counts('employees')
                    ->badge()
                    ->color('info'),

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
            'index' => Pages\ManageSalaryScales::route('/'),
        ];
    }
}

