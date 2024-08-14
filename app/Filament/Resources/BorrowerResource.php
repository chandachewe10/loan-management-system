<?php

namespace App\Filament\Resources;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
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

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Borrowers';

    protected static ?string $navigationGroup = 'Customers';
    


    //     public static function infolist(Infolist $infolist): Infolist
    //     {

    // // Fetch existing files associated with the borrower (assuming you have a $borrower variable available)
    // $borrowerFiles = $borrower->getMedia('attachments'); // Adjust the collection name as per your setup

    // $existingFilesInfo = $borrowerFiles->map(function (Media $file) {
    //     return [
    //         'name' => $file->file_name,
    //         'url' => $file->getFullUrl(),
    //     ];
    // });



    //         return $infolist
    //             ->schema([
    //                 Section::make('Personal Details')
    //                     ->description('Borrower Personal Details')
    //                     ->schema([
    //                         TextEntry::make('first_name'),
    //                         TextEntry::make('last_name'),
    //                         TextEntry::make('gender'),
    //                         TextEntry::make('dob'),
    //                         TextEntry::make('occupation'),
    //                         TextEntry::make('identification'),
    //                         TextEntry::make('mobile'),
    //                         TextEntry::make('email'),
    //                         TextEntry::make('address'),
    //                         TextEntry::make('city'),
    //                         TextEntry::make('province'),
    //                         TextEntry::make('zipcode'),
    //                     ])
    //                     ->columns(2),
    //                 Section::make('Next of Kin Details')
    //                     ->description('Borrower Next Of Kin Details')
    //                     ->schema([
    //                         TextEntry::make('next_of_kin_first_name'),
    //                         TextEntry::make('next_of_kin_last_name'),
    //                         TextEntry::make('phone_next_of_kin'),
    //                         TextEntry::make('address_next_of_kin'),
    //                         TextEntry::make('relationship_next_of_kin'),
    //                     ])
    //                     ->columns(2),
    //                     Section::make('Bank Details')
    //                     ->description('Borrower Bank Details')
    //                     ->schema([
    //                         TextEntry::make('bank_name'),
    //                         TextEntry::make('bank_branch'),
    //                         TextEntry::make('bank_sort_code'),
    //                         TextEntry::make('bank_account_number'),
    //                         TextEntry::make('bank_account_name'),
    //                         TextEntry::make('mobile_money_name'),
    //                         TextEntry::make('mobile_money_number'),
    //                     ])
    //                     ->columns(2),

    //                     Section::make('Borrower Files')
    //                     ->description('Borrower Attached Files')
    //                     ->schema([
    //                         TextEntry::make('existing_files')
    //                             ->label('Existing Files')
    //                             ->value($existingFilesInfo->implode('<br>'))
    //                             ->multiline() // Adjust if needed for multiline display
    //                     ])
    //                     ->columns(2),

    //             ]);
    //     }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }





    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('first_name')
                    ->label('First Name')
                    ->prefixIcon('heroicon-o-user')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name')
                    ->label('Last Name')
                    ->prefixIcon('heroicon-o-user')
                    ->required()
                    ->maxLength(255),

                    Forms\Components\TextInput::make('full_name')
                    ->hidden(),

                Forms\Components\Select::make('gender')
                    ->label('Gender')
                    ->prefixIcon('heroicon-o-users')
                    ->options([
                        'male' => 'Male',
                        'female' => 'female',

                    ])
                    ->required(),
                Forms\Components\DatePicker::make('dob')
                    ->label('Date of Birth')
                    ->prefixIcon('heroicon-o-calendar')
                    ->required()
                    ->native(false)
                    ->maxDate(now()),
                Forms\Components\Select::make('occupation')

                    ->options([
                        'employed' => 'Employed',
                        'self employed' => 'Self Employed',
                        'unemployed' => 'Un-Employed',
                        'student' => 'Student',

                    ])
                    ->prefixIcon('heroicon-o-briefcase')
                    ->required(),
                Forms\Components\TextInput::make('identification')
                    ->label('National ID')
                    ->prefixIcon('heroicon-o-identification')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('mobile')
                    ->label('Phone number')
                    ->prefixIcon('heroicon-o-phone')
                    ->tel()
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->label('Email address')
                    ->prefixIcon('heroicon-o-envelope')
                    ->email()

                    ->maxLength(255),
                Forms\Components\Textarea::make('address')
                    ->label('Address')

                    ->required()

                    ->maxLength(255),

                Forms\Components\TextInput::make('city')
                    ->label('City')
                    ->prefixIcon('fas-map-marker')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('province')
                    ->label('Province')
                    ->prefixIcon('fas-map-marker')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('zipcode')
                    ->label('Zipcode')
                    ->prefixIcon('fas-map-marker')
                    ->maxLength(255),
                Forms\Components\TextInput::make('next_of_kin_first_name')
                    ->label('Next of Kin First Name')
                    ->prefixIcon('fas-user')
                    ->maxLength(255),
                Forms\Components\TextInput::make('next_of_kin_last_name')
                    ->label('Next of Kin Last Name')
                    ->prefixIcon('fas-users')
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone_next_of_kin')
                    ->label('Phone Next of Kin')
                    ->prefixIcon('heroicon-o-phone')
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
                    ->prefixIcon('fas-building')
                    ->maxLength(255),
                Forms\Components\TextInput::make('bank_branch')
                    ->label('Bank Branch')
                    ->prefixIcon('fas-building')
                    ->maxLength(255),
                Forms\Components\TextInput::make('bank_sort_code')
                    ->label('Bank Sort Code')
                    ->prefixIcon('fas-building')
                    ->maxLength(255),
                Forms\Components\TextInput::make('bank_account_number')
                    ->label('Bank Account Number')
                    ->prefixIcon('fas-dollar-sign')
                    ->maxLength(255),
                Forms\Components\TextInput::make('bank_account_name')
                    ->label('Bank Account Name')
                    ->maxLength(255),

                Forms\Components\TextInput::make('mobile_money_name')
                    ->label('Mobile Money Name')
                    ->prefixIcon('fas-phone')
                    ->maxLength(255),
                Forms\Components\TextInput::make('mobile_money_number')
                    ->label('Mobile Money Number')
                    ->prefixIcon('fas-user')
                    ->tel(),
                SpatieMediaLibraryFileUpload::make('attachment')
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

                Tables\Columns\TextColumn::make('occupation')
                    ->searchable(),
                Tables\Columns\TextColumn::make('identification')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mobile')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),


            ])
            
            ->filters([
                Tables\Filters\SelectFilter::make('gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',

                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),

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
            'index' => Pages\ListBorrowers::route('/'),
            'create' => Pages\CreateBorrower::route('/create'),
            'view' => Pages\ViewBorrower::route('/{record}'),
            'edit' => Pages\EditBorrower::route('/{record}/edit'),
        ];
    }
}
