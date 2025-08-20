<?php

namespace App\Filament\Resources\SwitchBranchResource\Pages;

use App\Filament\Resources\SwitchBranchResource;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use App\Models\Branches;
use Filament\Actions;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;

class CreateSwitchBranch extends CreateRecord
{
    protected static string $resource = SwitchBranchResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        try {

            $branchId = $data['branch_id'];
            if ($branchId == 0) {
                $branchId = NULL;

            }

            $user = User::find(auth()->id());
            $branchName = Branches::find($branchId)->branch_name ?? 'MAIN BRANCH';
            $user->update([
                'branch_id' => $branchId
            ]);

            Notification::make()
                ->title('Branch Switch Successful')
                ->body("You have successfully switched to branch: " . $branchName)
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
        }
    }


protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('New Branch')
            ->body('Welcome to the New Branch');
    }


    protected function getRedirectUrl(): string
    {

        return '/admin';
    }
}
