<?php
namespace App\Filament\Pages\Auth;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Component;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Support\Facades\Auth;

class Register extends BaseRegister
{
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),

                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getNameFormComponent(): Component
    {
        return TextInput::make('name')
                    ->label('Business Name')
                    ->helperText('We will send you a verification email. Make sure to put a valid working email address and verify from the same device which is signing up')
                    ->required()
                    ->maxLength(255);
    }

    /**
     * Get the URL that the user should be redirected to after registration.
     */
    public function getRedirectUrl(): string
    {
        // After registration, check if profile is incomplete
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isProfileIncomplete() && !$user->profile_completion_modal_shown) {
                return '/admin/profile-completion';
            }
        }
        
        return parent::getRedirectUrl();
    }
}
