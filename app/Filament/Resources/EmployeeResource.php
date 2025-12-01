<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Employees';
    protected static ?string $navigationGroup = 'Payroll';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personal Information')
                    ->schema([
                        Forms\Components\TextInput::make('employee_number')
                            ->label('Employee Number')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->prefixIcon('heroicon-o-identification')
                            ->default(fn () => IdGenerator::generate([
                                'table' => 'employees',
                                'field' => 'employee_number',
                                'length' => 8,
                                'prefix' => 'EMP-'
                            ])),

                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-user'),

                        Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-user'),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-envelope'),

                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-phone'),

                        Forms\Components\TextInput::make('national_id')
                            ->label('National ID (NRC)')
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-identification'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Employment Details')
                    ->schema([
                        Forms\Components\TextInput::make('position')
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-briefcase'),

                        Forms\Components\TextInput::make('department')
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-building-office'),

                        Forms\Components\DatePicker::make('date_of_employment')
                            ->required()
                            ->native(false)
                            ->prefixIcon('heroicon-o-calendar'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active Employee')
                            ->default(true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Salary Scale & Structure')
                    ->description('Assign a salary scale or set individual salary components')
                    ->schema([
                        Forms\Components\Select::make('salary_scale_id')
                            ->label('Salary Scale')
                            ->relationship('salaryScale', 'scale_name', fn ($query) => $query->where('is_active', true))
                            ->searchable()
                            ->preload()
                            ->prefixIcon('heroicon-o-scale')
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                if ($state) {
                                    $scale = \App\Models\SalaryScale::find($state);
                                    if ($scale) {
                                        $set('basic_salary', $scale->basic_salary);
                                        $set('housing_allowance', $scale->housing_allowance);
                                        $set('transport_allowance', $scale->transport_allowance);
                                        $set('medical_allowance', $scale->medical_allowance);
                                        $set('other_allowances', $scale->other_allowances);
                                    }
                                }
                            })
                            ->helperText('Select a salary scale to auto-fill salary components, or leave empty to set manually')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('scale_name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('basic_salary')
                                    ->numeric()
                                    ->required(),
                            ])
                            ->createOptionUsing(function (array $data) {
                                return \App\Models\SalaryScale::create($data)->id;
                            }),

                        Forms\Components\TextInput::make('basic_salary')
                            ->label('Basic Salary (ZMW)')
                            ->required()
                            ->numeric()
                            ->prefixIcon('heroicon-o-currency-dollar')
                            ->helperText('Can be set manually or auto-filled from salary scale'),

                        Forms\Components\TextInput::make('housing_allowance')
                            ->label('Housing Allowance (ZMW)')
                            ->numeric()
                            ->default(0)
                            ->prefixIcon('heroicon-o-home'),

                        Forms\Components\TextInput::make('transport_allowance')
                            ->label('Transport Allowance (ZMW)')
                            ->numeric()
                            ->default(0)
                            ->prefixIcon('heroicon-o-truck'),

                        Forms\Components\TextInput::make('medical_allowance')
                            ->label('Medical Allowance (ZMW)')
                            ->numeric()
                            ->default(0)
                            ->prefixIcon('heroicon-o-heart'),

                        Forms\Components\TextInput::make('other_allowances')
                            ->label('Other Allowances (ZMW)')
                            ->numeric()
                            ->default(0)
                            ->prefixIcon('heroicon-o-plus-circle'),

                        Forms\Components\Placeholder::make('gross_salary_preview')
                            ->label('Gross Salary Preview')
                            ->content(fn (Forms\Get $get) => number_format(
                                ($get('basic_salary') ?? 0) +
                                ($get('housing_allowance') ?? 0) +
                                ($get('transport_allowance') ?? 0) +
                                ($get('medical_allowance') ?? 0) +
                                ($get('other_allowances') ?? 0),
                                2
                            ) . ' ZMW')
                            ->extraAttributes(['class' => 'font-bold text-lg text-green-600']),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Bank Details')
                    ->schema([
                        Forms\Components\TextInput::make('bank_name')
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-building-library'),

                        Forms\Components\TextInput::make('bank_account_number')
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-credit-card'),

                        Forms\Components\TextInput::make('bank_branch')
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-building-office'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee_number')
                    ->label('Employee #')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('first_name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('last_name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('position')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('department')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('salaryScale.scale_name')
                    ->label('Salary Scale')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('basic_salary')
                    ->label('Basic Salary')
                    ->money('ZMW')
                    ->sortable(),

                Tables\Columns\TextColumn::make('gross_salary')
                    ->label('Gross Salary')
                    ->money('ZMW')
                    ->getStateUsing(fn (Employee $record) => $record->gross_salary)
                    ->sortable()
                    ->color('success'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
                Tables\Filters\SelectFilter::make('department')
                    ->options(fn () => Employee::query()->distinct()->pluck('department', 'department')->toArray()),
                Tables\Filters\SelectFilter::make('salary_scale_id')
                    ->label('Salary Scale')
                    ->relationship('salaryScale', 'scale_name'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}

