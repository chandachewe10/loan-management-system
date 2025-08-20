<?php

namespace App\Filament\Resources\SwitchBranchResource\Pages;

use App\Filament\Resources\SwitchBranchResource;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
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

            $user = User::find(auth()->id());
            $user->update([
                'branch_id' => $branchId
            ]);

            Notification::make()
                ->title('Branch Switch Successful')
                ->body("You have successfully switched to branch: " . \App\Models\Branches::find($branchId)->branch_name)
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

    protected function getRedirectUrl(): string
    {

        return '/admin';
    }
}
