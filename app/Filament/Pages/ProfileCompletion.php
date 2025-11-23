<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;

class ProfileCompletion extends Page implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    
    protected static string $view = 'filament.pages.profile-completion';
    
    protected static bool $shouldRegisterNavigation = false;
    
    public ?array $data = [];
    
    public bool $showModal = false;

    public function mount(): void
    {
        $user = Auth::user();
        
        // Check if profile is incomplete
        if ($user->isProfileIncomplete() && !$user->profile_completion_modal_shown) {
            $this->showModal = true;
        }
        
        $this->form->fill([
            'name' => $user->name,
            'company_representative' => $user->company_representative,
            'company_representative_phone' => $user->company_representative_phone,
            'company_representative_email' => $user->company_representative_email,
            'company_phone' => $user->company_phone,
            'company_address' => $user->company_address,
        ]);
        
    }

    public function form(Form $form): Form
    {
        return $form
            ->model(Auth::user())
            ->schema([
                Section::make('Complete Your Company Profile')
                    ->description('Please provide the following information to complete your company profile.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Business Name')
                            ->required()
                            ->disabled()
                            ->dehydrated()
                            ->columnSpan(2),
                        
                        TextInput::make('company_representative')
                            ->label('Company Representative')
                            ->placeholder('Enter Name')
                            ->maxLength(255)
                            ->columnSpan(2),
                        
                        TextInput::make('company_representative_phone')
                            ->label('Company Representative Phone Number')
                            ->placeholder('Enter representative phone number')
                            ->tel()
                            ->maxLength(255),
                        
                        TextInput::make('company_representative_email')
                            ->label('Company Representative Email')
                            ->placeholder('Enter representative email')
                            ->email()
                            ->maxLength(255),
                        
                        TextInput::make('company_phone')
                            ->label('Company Phone Number')
                            ->placeholder('Enter company phone number')
                            ->tel()
                            ->maxLength(255),
                        
                        Textarea::make('company_address')
                            ->label('Company Address')
                            ->placeholder('Enter company address')
                            ->rows(3)
                            ->maxLength(500),
                        
                        SpatieMediaLibraryFileUpload::make('company_logo')
                            ->label('Company Logo')
                            ->collection('company_logo')
                            ->image()
                            ->imageEditor()
                            ->maxSize(2048)
                            ->disk('company_logos')
                            ->visibility('public')
                            ->helperText('Upload your company logo (max 2MB)')
                            ->columnSpan(2),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = Auth::user();
        
        $user->update([
            'name' => $data['name'],
            'company_representative' => $data['company_representative'] ?? null,
            'company_representative_phone' => $data['company_representative_phone'] ?? null,
            'company_representative_email' => $data['company_representative_email'] ?? null,
            'company_phone' => $data['company_phone'] ?? null,
            'company_address' => $data['company_address'] ?? null,
            'profile_completion_modal_shown' => true,
        ]);
        
        // Handle logo upload if present - SpatieMediaLibraryFileUpload handles this automatically
        // The file is already uploaded when the form is submitted
        
        Notification::make()
            ->success()
            ->title('Profile Updated')
            ->body('Your company profile has been updated successfully.')
            ->send();
        
        $this->showModal = false;
        
        // Redirect to dashboard
        $this->redirect(route('filament.admin.pages.dashboard'));
    }

    public function completeLater(): void
    {
        $user = Auth::user();
        $user->update([
            'profile_completion_modal_shown' => true,
        ]);
        
        $this->showModal = false;
        
        Notification::make()
            ->info()
            ->title('Profile Incomplete')
            ->body('You can complete your profile later from your profile settings.')
            ->send();
        
        // Redirect to dashboard
        $this->redirect(route('filament.admin.pages.dashboard'));
    }

    public function getProfileCompleteness(): int
    {
        return Auth::user()->profile_completeness;
    }

    protected function getMaxWidth(): MaxWidth
    {
        return MaxWidth::Full;
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save & Continue')
                ->submit('save'),
        ];
    }
}

