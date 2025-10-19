<?php

namespace App\Filament\Resources\WalletResource\Pages;

use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\WalletResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateWallet extends CreateRecord
{
    protected static string $resource = WalletResource::class;

    protected function handleRecordCreation(array $data): Model
    {

        $wallet = auth()->user()->createWallet([
            'name' => $data['name'],
            'amount' => 0,
            'organization_id' => auth()->user()->organization_id,
            'branch_id' => auth()->user()->branch_id,
            'slug' => $this->generateSlug($data['name']),
            'meta' => $data['meta'],
            'description' => strip_tags($data['description']),
        ])->deposit($data['amount']);

       $wallet->organization_id =  auth()->user()->organization_id;
       $wallet->save();

        return $wallet;
    }

    protected function generateSlug($text)
    {
        // Replace spaces with hyphens
        $slug = str_replace(' ', '-', $text);

        // Remove special characters
        $slug = preg_replace('/[^A-Za-z0-9\-]/', '', $slug);

        // Convert to lowercase
        $slug = strtolower($slug);

        return $slug;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }


    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Bank Wallet')
            ->body('The virtual Bank Wallet has been created successfully.');
    }
}
