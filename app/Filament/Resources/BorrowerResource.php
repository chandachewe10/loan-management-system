<?php

namespace App\Filament\Resources;

use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;
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
use App\Filament\Exports\BorrowerExporter;
use Filament\Tables\Actions\ExportAction;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\FontWeight;



class BorrowerResource extends Resource
{
    protected static ?string $model = Borrower::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Borrowers';

    protected static ?string $navigationGroup = 'Customers';



    public static function infolist(Infolist $infolist): Infolist
    {


        $borrower = $infolist->getRecord();

        return $infolist
            ->schema([

                Section::make('Loan History')
                    ->description('Current and previous loans with AI insights')
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('loans')
                            ->label('')
                            ->getStateUsing(function ($record) {
                                return \App\Models\Loan::withoutGlobalScopes()
                                    ->where('borrower_id', $record->id)
                                    
                                    ->orderBy('created_at', 'desc') 
                                    ->get()
                                    ->toArray();
                            })
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        // Loan Basic Info
                                        Section::make('Loan Details')
                                            ->schema([
                                                TextEntry::make('loan_number')
                                                    ->label('Loan #')
                                                    ->badge()
                                                    ->color('primary'),

                                                TextEntry::make('loan_type.loan_name')
                                                    ->label('Type')
                                                    ->badge()
                                                    ->color('gray'),

                                                TextEntry::make('principal_amount')
                                                    ->label('Principal')
                                                    ->money('ZMW')
                                                    ->weight(FontWeight::Bold),

                                                TextEntry::make('repayment_amount')
                                                    ->label('Total Repayment')
                                                    ->weight(FontWeight::Bold)
                                                    ->money('ZMW')
                                                    ->color('success'),

                                                TextEntry::make('balance')
                                                    ->label('Outstanding Balance')
                                                    ->weight(FontWeight::Bold)
                                                    ->money('ZMW')
                                                    ->color(fn($state) => $state > 0 ? 'danger' : 'success'),
                                            ])
                                            ->columns(2),

                                        // Dates & Status
                                        Section::make('Timeline')
                                            ->schema([
                                                TextEntry::make('loan_release_date')
                                                    ->label('Release Date')
                                                    ->date('M j, Y')
                                                    ->icon('heroicon-o-calendar'),

                                                TextEntry::make('loan_due_date')
                                                    ->label('Due Date')
                                                    ->date('M j, Y')
                                                    ->icon('heroicon-o-calendar')
                                                    ->color(
                                                        fn($state, $record) =>
                                                        now()->gt($state) && $record['balance'] > 0 ? 'danger' : 'gray'
                                                    ),

                                                TextEntry::make('loan_status')
                                                    ->label('Status')
                                                    ->badge()
                                                    ->color(fn(string $state): string => match ($state) {
                                                        'requested' => 'gray',
                                                        'processing' => 'info',
                                                        'approved' => 'success',
                                                        'fully_paid' => 'success',
                                                        'denied' => 'danger',
                                                        'defaulted' => 'warning',
                                                        'partially_paid' => 'warning',
                                                        default => 'gray',
                                                    }),

                                                TextEntry::make('loan_duration')
                                                    ->label('Duration')
                                                    ->formatStateUsing(fn($state) => "{$state} months")
                                                    ->icon('heroicon-o-clock'),
                                            ])
                                            ->columns(2),

                                        // AI Insights & Financials
                                        Section::make('AI Assessment & Financials')
                                            ->schema([
                                                // AI Credit Score
                                                TextEntry::make('ai_credit_score')
                                                    ->label('AI Credit Score')
                                                    ->weight(FontWeight::Bold)
                                                    ->formatStateUsing(fn($state) => $state ? number_format($state) : 'N/A')
                                                    ->color(fn($state) => match (true) {
                                                        $state >= 700 => 'success',
                                                        $state >= 600 => 'warning',
                                                        $state >= 500 => 'orange',
                                                        $state > 0 => 'danger',
                                                        default => 'gray',
                                                    })
                                                    ->icon('heroicon-o-cpu-chip'),

                                                // AI Recommendation
                                                TextEntry::make('ai_recommendation')
                                                    ->label('AI Recommendation')
                                                    ->formatStateUsing(fn($state) => $state ?: 'Not Assessed')
                                                    ->badge()
                                                    ->color(fn($state) => match ($state) {
                                                        'APPROVE' => 'success',
                                                        'REVIEW' => 'warning',
                                                        'REJECT' => 'danger',
                                                        default => 'gray',
                                                    }),

                                                // Default Probability
                                                TextEntry::make('default_probability')
                                                    ->label('Default Risk')
                                                    ->weight(FontWeight::Bold)
                                                    ->formatStateUsing(fn($state) => $state ? number_format($state * 100, 1) . '%' : 'N/A')
                                                    ->color(fn($state) => match (true) {
                                                        $state > 0.3 => 'danger',
                                                        $state > 0.2 => 'warning',
                                                        $state > 0.1 => 'orange',
                                                        $state > 0 => 'success',
                                                        default => 'gray',
                                                    }),

                                                // Service Fee
                                                TextEntry::make('service_fee')
                                                    ->label('Service Fee')
                                                    ->weight(FontWeight::Bold)
                                                    ->money('ZMW')
                                                    ->color('blue'),

                                                // Disbursed Amount
                                                TextEntry::make('disbursed_amount')
                                                    ->label('Amount Disbursed')
                                                    ->money('ZMW')
                                                    ->weight(FontWeight::Bold)
                                                    ->color('green'),
                                            ])
                                            ->columns(2),
                                    ]),

                                // Risk Factors (if available)
                                Section::make('AI Risk Factors')
                                    ->visible(fn($record) => !empty($record['risk_factors']))
                                    ->schema([
                                        Infolists\Components\RepeatableEntry::make('risk_factors')
                                            ->getStateUsing(fn($record) => $record['risk_factors'] ?? [])
                                            ->schema([
                                                TextEntry::make('factor')
                                                    ->getStateUsing(fn($state) => $state)
                                                    ->icon('heroicon-o-exclamation-triangle')
                                                    ->color('danger')
                                                    ->extraAttributes(['class' => 'bg-red-50 px-3 py-2 rounded']),
                                            ])
                                            ->columns(1)
                                            ->label('No risk factors identified'),
                                    ])
                                    ->collapsible(),

                                // AI Decision Reason
                                TextEntry::make('ai_decision_reason')
                                    ->label('AI Analysis')
                                    ->visible(fn($record) => !empty($record['ai_decision_reason']))
                                    ->columnSpanFull()
                                    ->extraAttributes(['class' => 'bg-blue-50 px-4 py-3 rounded-lg border border-blue-200']),
                            ])
                            ->columns(1)
                            ->visible(
                                fn($record) => \App\Models\Loan::withoutGlobalScopes()
                                    ->where('borrower_id', $record->id)
                                    ->exists()
                            ),

                        // Show message when no loans exist
                        TextEntry::make('no_loans_placeholder')
                            ->label('')
                            ->default('No loans found for this borrower')
                            ->color('gray')
                            ->visible(
                                fn($record) => !\App\Models\Loan::withoutGlobalScopes()
                                    ->where('borrower_id', $record->id)
                                    ->exists()
                            ),
                    ])
                    ->collapsible(),
                Section::make('Personal Details')
                    ->description('Borrower Personal Details')
                    ->icon('heroicon-o-user-circle')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('first_name')
                                    ->icon('heroicon-o-user'),
                                TextEntry::make('last_name')
                                    ->icon('heroicon-o-user'),
                                TextEntry::make('gender')
                                    ->icon('heroicon-o-sparkles'),
                                TextEntry::make('dob')
                                    ->label('Date of Birth')
                                    ->date('j F Y')
                                    ->icon('heroicon-o-cake'),
                                TextEntry::make('occupation')
                                    ->icon('heroicon-o-briefcase'),
                                TextEntry::make('identification')
                                    ->icon('heroicon-o-identification'),
                                TextEntry::make('mobile')
                                    ->icon('heroicon-o-phone'),
                                TextEntry::make('email')
                                    ->icon('heroicon-o-envelope'),
                                TextEntry::make('address')
                                    ->icon('heroicon-o-map-pin')
                                    ->columnSpanFull(),
                                TextEntry::make('city')
                                    ->icon('heroicon-o-building-office'),
                                TextEntry::make('province')
                                    ->icon('heroicon-o-map'),
                                TextEntry::make('zipcode')
                                    ->icon('heroicon-o-tag'),
                            ]),
                    ]),

                Section::make('Next of Kin Details')
                    ->description('Borrower Next Of Kin Details')
                    ->icon('heroicon-o-user-group')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('next_of_kin_first_name')
                                    ->icon('heroicon-o-user'),
                                TextEntry::make('next_of_kin_last_name')
                                    ->icon('heroicon-o-user'),
                                TextEntry::make('phone_next_of_kin')
                                    ->icon('heroicon-o-phone'),
                                TextEntry::make('address_next_of_kin')
                                    ->icon('heroicon-o-map-pin'),
                                TextEntry::make('relationship_next_of_kin')
                                    ->icon('heroicon-o-heart')
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Section::make('Bank Details')
                    ->description('Borrower Bank Details')
                    ->icon('heroicon-o-building-library')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('bank_name')
                                    ->icon('heroicon-o-building-office'),
                                TextEntry::make('bank_branch')
                                    ->icon('heroicon-o-building-office'),
                                TextEntry::make('bank_sort_code')
                                    ->icon('heroicon-o-banknotes'),
                                TextEntry::make('bank_account_number')
                                    ->icon('heroicon-o-credit-card'),
                                TextEntry::make('bank_account_name')
                                    ->icon('heroicon-o-user'),
                                TextEntry::make('mobile_money_name')
                                    ->icon('heroicon-o-device-phone-mobile'),
                                TextEntry::make('mobile_money_number')
                                    ->icon('heroicon-o-phone'),
                            ]),
                    ]),



                Section::make('Attached Files')
                    ->schema([
                        Actions::make(
                            array_merge(
                                ...$borrower->getMedia('payslips')->map(function ($media) {
                                    return [
                                        Action::make('download_' . $media->id)
                                            ->label('Download Payslip')
                                            ->icon('heroicon-o-arrow-down-tray')
                                            ->url($media->getUrl())
                                            ->openUrlInNewTab()
                                            ->outlined()
                                            ->color('primary'),

                                        Action::make('view_' . $media->id)
                                            ->label('View Payslip')
                                            ->icon('heroicon-o-eye')
                                            ->url($media->getUrl())
                                            ->openUrlInNewTab()
                                            ->outlined()
                                            ->color('secondary'),
                                    ];
                                })->toArray()
                            )
                        ),

                        Actions::make(
                            array_merge(
                                ...$borrower->getMedia('bank_statements')->map(function ($media) {
                                    return [
                                        Action::make('download_' . $media->id)
                                            ->label('Download Bank Statement')
                                            ->icon('heroicon-o-arrow-down-tray')
                                            ->url($media->getUrl())
                                            ->openUrlInNewTab()
                                            ->outlined()
                                            ->color('primary'),

                                        Action::make('view_' . $media->id)
                                            ->label('View Bank Statement')
                                            ->icon('heroicon-o-eye')
                                            ->url($media->getUrl())
                                            ->openUrlInNewTab()
                                            ->outlined()
                                            ->color('secondary'),
                                    ];
                                })->toArray()
                            )
                        ),

                        Actions::make(
                            array_merge(
                                ...$borrower->getMedia('nrc')->map(function ($media) {
                                    return [
                                        Action::make('download_' . $media->id)
                                            ->label('Download Nrc')
                                            ->icon('heroicon-o-arrow-down-tray')
                                            ->url($media->getUrl())
                                            ->openUrlInNewTab()
                                            ->outlined()
                                            ->color('primary'),

                                        Action::make('view_' . $media->id)
                                            ->label('View Nrc')
                                            ->icon('heroicon-o-eye')
                                            ->url($media->getUrl())
                                            ->openUrlInNewTab()
                                            ->outlined()
                                            ->color('secondary'),
                                    ];
                                })->toArray()
                            )
                        ),

                        Actions::make(
                            array_merge(
                                ...$borrower->getMedia('proof_of_residence')->map(function ($media) {
                                    return [
                                        Action::make('download_' . $media->id)
                                            ->label('Download Proof of Residence')
                                            ->icon('heroicon-o-arrow-down-tray')
                                            ->url($media->getUrl())
                                            ->openUrlInNewTab()
                                            ->outlined()
                                            ->color('primary'),

                                        Action::make('view_' . $media->id)
                                            ->label('View Proof of Residence')
                                            ->icon('heroicon-o-eye')
                                            ->url($media->getUrl())
                                            ->openUrlInNewTab()
                                            ->outlined()
                                            ->color('secondary'),
                                    ];
                                })->toArray()
                            )
                        ),

                        Actions::make(
                            array_merge(
                                ...$borrower->getMedia('preapproval_letter')->map(function ($media) {
                                    return [
                                        Action::make('download_' . $media->id)
                                            ->label('Download Preapproval Letter')
                                            ->icon('heroicon-o-arrow-down-tray')
                                            ->url($media->getUrl())
                                            ->openUrlInNewTab()
                                            ->outlined()
                                            ->color('primary'),

                                        Action::make('view_' . $media->id)
                                            ->label('View Preapproval Letter')
                                            ->icon('heroicon-o-eye')
                                            ->url($media->getUrl())
                                            ->openUrlInNewTab()
                                            ->outlined()
                                            ->color('secondary'),
                                    ];
                                })->toArray()
                            )
                        ),

                        Actions::make(
                            array_merge(
                                ...$borrower->getMedia('collaterals')->map(function ($media) {
                                    return [
                                        Action::make('download_' . $media->id)
                                            ->label('Download Collateral')
                                            ->icon('heroicon-o-arrow-down-tray')
                                            ->url($media->getUrl())
                                            ->openUrlInNewTab()
                                            ->outlined()
                                            ->color('primary'),

                                        Action::make('view_' . $media->id)
                                            ->label('View Collateral')
                                            ->icon('heroicon-o-eye')
                                            ->url($media->getUrl())
                                            ->openUrlInNewTab()
                                            ->outlined()
                                            ->color('secondary'),
                                    ];
                                })->toArray()
                            )
                        ),

                    ]),
            ]);
    }


    protected static function getFileDisplaySchema(): array
    {
        return [
            Grid::make(3)
                ->schema([
                    IconEntry::make('file_icon')
                        ->icon(fn($state, $record) => self::getFileIcon($record['extension']))
                        ->size(IconEntry\IconEntrySize::Large)
                        ->color('primary'),

                    TextEntry::make('file_name')
                        ->weight(FontWeight::Bold)
                        ->size(TextEntry\TextEntrySize::Large),

                    TextEntry::make('file_size')
                        ->color('gray')
                        ->formatStateUsing(fn($state) => self::formatFileSize($state)),
                ]),

            Actions::make([
                Action::make('view_file')
                    ->label('View File')
                    ->icon('heroicon-o-eye')
                    ->url(fn($record) => $record['url'])
                    ->openUrlInNewTab()
                    ->size(ActionSize::Small),

                Action::make('download_file')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn($record) => $record['url'] . '?download=1')
                    ->size(ActionSize::Small),
            ])->verticalAlignment('center'),
        ];
    }
    // Helper method to get appropriate file icons
    protected static function getFileIcon(string $extension): string
    {
        $iconMap = [
            'pdf' => 'heroicon-o-document-text',
            'doc' => 'heroicon-o-document',
            'docx' => 'heroicon-o-document',
            'xls' => 'heroicon-o-table-cells',
            'xlsx' => 'heroicon-o-table-cells',
            'jpg' => 'heroicon-o-photo',
            'jpeg' => 'heroicon-o-photo',
            'png' => 'heroicon-o-photo',
            'gif' => 'heroicon-o-photo',
            'txt' => 'heroicon-o-document',
            'zip' => 'heroicon-o-archive-box',
            'rar' => 'heroicon-o-archive-box',
        ];

        return $iconMap[strtolower($extension)] ?? 'heroicon-o-document';
    }

    // Helper method to format file size
    protected static function formatFileSize(int $size): string
    {
        if ($size == 0) return '0 Bytes';

        $units = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($size, 1024));

        return round($size / pow(1024, $i), 2) . ' ' . $units[$i];
    }

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
                    ->required()
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
                SpatieMediaLibraryFileUpload::make('payslips')
                    ->disk('borrowers')
                    ->collection('payslips')
                    ->visibility('public')
                    ->multiple()
                    ->minFiles(0)
                    ->maxFiles(10)
                    ->maxSize(5120)
                    ->columnSpan(2)
                    ->openable(),
                SpatieMediaLibraryFileUpload::make('bank_statements')
                    ->disk('borrowers')
                    ->collection('bank_statements')
                    ->visibility('public')
                    ->multiple()
                    ->minFiles(0)
                    ->maxFiles(10)
                    ->maxSize(5120)
                    ->columnSpan(2)
                    ->openable(),
                SpatieMediaLibraryFileUpload::make('nrc')
                    ->disk('borrowers')
                    ->collection('nrc')
                    ->visibility('public')
                    ->maxSize(5120)
                    ->columnSpan(2)
                    ->openable(),
                SpatieMediaLibraryFileUpload::make('preapproval_letter')
                    ->disk('borrowers')
                    ->collection('preapproval_letter')
                    ->visibility('public')
                    ->minFiles(0)
                    ->maxSize(5120)
                    ->columnSpan(2)
                    ->openable(),
                SpatieMediaLibraryFileUpload::make('proof_of_residence')
                    ->disk('borrowers')
                    ->collection('proof_of_residence')
                    ->visibility('public')
                    ->minFiles(0)
                    ->maxSize(5120)
                    ->columnSpan(2)
                    ->openable(),
                SpatieMediaLibraryFileUpload::make('collaterals')
                    ->disk('borrowers')
                    ->multiple()
                    ->minFiles(0)
                    ->maxFiles(10)
                    ->collection('collaterals')
                    ->visibility('public')
                    ->maxSize(5120)
                    ->columnSpan(2)
                    ->openable(),




            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ExportAction::make()
                    ->exporter(BorrowerExporter::class)
            ])
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
                Tables\Columns\TextColumn::make('created_by.name')
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
