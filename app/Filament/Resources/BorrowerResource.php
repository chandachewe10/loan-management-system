<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BorrowerResource\Pages;
use App\Filament\Resources\BorrowerResource\RelationManagers;
use App\Models\Borrower;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BorrowerResource extends Resource
{
    protected static ?string $model = Borrower::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('first_name')
                    ->label('First Name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name')
                    ->label('Last Name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('gender')
                    ->label('Gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'female',

                    ])
                    ->required(),
                Forms\Components\DatePicker::make('dob')
                    ->label('Date of Birth')
                    ->required()
                    ->maxDate(now()),
                Forms\Components\Select::make('occupation')

                    ->options([
                        'employed' => 'Employed',
                        'self employed' => 'Self Employed',
                        'unemployed' => 'Un-Employed',
                        'student' => 'Student',

                    ])
                    ->required(),
                Forms\Components\TextInput::make('identification')
                    ->label('National ID')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('mobile')
                    ->label('Phone number')
                    ->tel()
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->label('Email address')
                    ->email()

                    ->maxLength(255),
                Forms\Components\Textarea::make('address')
                    ->label('Address')
                    ->required()

                    ->maxLength(255),

                Forms\Components\TextInput::make('city')
                    ->label('City')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('province')
                    ->label('Province')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('zipcode')
                    ->label('Zipcode')

                    ->maxLength(255),
                Forms\Components\TextInput::make('next_of_kin_first_name')
                    ->label('Next of Kin First Name')

                    ->maxLength(255),
                Forms\Components\TextInput::make('next_of_kin_last_name')
                    ->label('Next of Kin Last Name')

                    ->maxLength(255),
                Forms\Components\TextInput::make('phone_next_of_kin')
                    ->label('Phone Next of Kin')
                    ->tel(),
                Forms\Components\Textarea::make('address_next_of_kin')

                    ->maxLength(255),
                Forms\Components\Select::make('relationship_next_of_kin')
                    ->label('Relationship to Next of Kin')
                    ->options([
                        'mom' => 'Mom',
                        'father' => 'Father',
                        'aunty' => 'Aunty',
                        'uncle' => 'Uncle',
                        'cousin' => 'Cousin',
                        'wife' => 'Wife',
                        'husband' => 'Husband',
                        'brother' => 'Brother',
                        'Sister' => 'sister',

                    ]),
                Forms\Components\TextInput::make('bank_name')
                    ->label('Bank Name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('bank_branch')
                    ->label('Bank Branch')
                    ->maxLength(255),
                Forms\Components\TextInput::make('bank_sort_code')
                    ->label('Bank Sort Code')
                    ->maxLength(255),
                Forms\Components\TextInput::make('bank_account_number')
                    ->label('Bank Account Number')
                    ->maxLength(255),
                Forms\Components\TextInput::make('bank_account_name')
                    ->label('Bank Account Name')
                    ->maxLength(255),

                Forms\Components\TextInput::make('mobile_money_name')
                    ->label('Mobile Money Name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('mobile_money_number')
                    ->label('Mobile Money Number')
                    ->tel(),
                Forms\Components\FileUpload::make('attachment')
                    ->disk('borrowers')
                    ->visibility('public')
                    ->multiple()
                    ->minFiles(0)
                    ->maxFiles(10)
                    ->maxSize(5120)
                    ->columnSpan(2)
                    ->openable()
                   

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('gender')
                    ->searchable(),
                Tables\Columns\TextColumn::make('dob')
                    ->searchable(),
                Tables\Columns\TextColumn::make('occupation')
                    ->searchable(),
                Tables\Columns\TextColumn::make('identification')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mobile')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->searchable(),
                Tables\Columns\TextColumn::make('province')
                    ->searchable(),
                Tables\Columns\TextColumn::make('zipcode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListBorrowers::route('/'),
            'create' => Pages\CreateBorrower::route('/create'),
            'edit' => Pages\EditBorrower::route('/{record}/edit'),
        ];
    }
}
