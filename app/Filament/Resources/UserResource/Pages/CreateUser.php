<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Spatie\Permission\Models\Role;
use App\Models\Payments;
use App\Models\User;
use App\Models\Branches;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): Model
    {





            $branchId = $data['branch_id'];
            if ($branchId == 0) {
                $branchId = NULL;
            }




            $user = \App\Models\User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'organization_id' => auth()->user()->organization_id,
                'branch_id' => $data['branch_id'],

            ]);


            return $user;

    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }



    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('User created')
            ->body('The User has been created successfully.');
    }
}
