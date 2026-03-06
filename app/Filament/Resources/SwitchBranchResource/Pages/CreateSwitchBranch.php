<?php

namespace App\Filament\Resources\SwitchBranchResource\Pages;

use App\Filament\Resources\SwitchBranchResource;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use App\Models\Branches;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSwitchBranch extends CreateRecord
{
    protected static string $resource = SwitchBranchResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        try {
            $branchId = $data['branch_id'];

            // Normalize: 0 means Main Branch (null)
            if ($branchId == 0) {
                $branchId = null;
            }

            $currentBranchId = auth()->user()->branch_id;

            // Prevent switching to the same branch
            if ((string) $branchId === (string) $currentBranchId) {
                $currentBranchName = Branches::find($currentBranchId)->branch_name ?? 'Main Branch';

                Notification::make()
                    ->title('Already in this branch')
                    ->body("You are already in \"{$currentBranchName}\". Please select a different branch to switch to.")
                    ->warning()
                    ->persistent()
                    ->send();

                $this->halt();
            }

            $user = User::find(auth()->id());
            $branchName = Branches::find($branchId)?->branch_name ?? 'Main Branch';

            $user->update(['branch_id' => $branchId]);

            Notification::make()
                ->title('Branch Switch Successful')
                ->body("You have successfully switched to: {$branchName}")
                ->success()
                ->persistent()
                ->send();

            return $user;

        } catch (\Exception $e) {
            Notification::make()
                ->title('Branch Switch Failed')
                ->body("Switching branches not successful: " . $e->getMessage())
                ->danger()
                ->persistent()
                ->send();

            $this->halt();
        }
    }

    protected function getCreatedNotification(): ?Notification
    {
        return null; // Custom notifications handled above
    }

    protected function getRedirectUrl(): string
    {
        return '/admin';
    }
}
