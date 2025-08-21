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
        $maximumUsersAllowed = \App\Models\User::where('organization_id', auth()->user()->organization_id)->count();
        $payments = Payments::where('organization_id', auth()->user()->organization_id)->latest()->first();

        if ($payments->payment_amount == 0) {

            Notification::make()
                ->warning()
                ->title('Upgrade Payment Plan')
                ->body('Please upgrade your payment plan. Your current payment plan of free trial is limited to one user.')
                ->persistent()
                ->send();
            $this->halt();
        }

        if ($payments->payment_amount == 990) {

            Notification::make()
                ->warning()
                ->title('Upgrade Payment Plan')
                ->body('Please upgrade your payment plan. Your current payment plan is limited to one user.')
                ->persistent()
                ->send();
            $this->halt();
        } elseif ($payments->payment_amount == 1320 && $maximumUsersAllowed == 2) {
            Notification::make()
                ->warning()
                ->title('Upgrade Payment Plan')
                ->body('Please upgrade your payment plan. Your current payment plan is limited to two users.')
                ->persistent()
                ->send();
            $this->halt();
        } else {

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

            // if($user){
            //     $roleNames = $data['roles'];


            //     $roles = Role::whereIn('name', $roleNames)->get();


            //     $user->assignRole($roles);
            // }
            return $user;
        }
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
