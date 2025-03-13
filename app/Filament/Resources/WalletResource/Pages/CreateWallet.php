<?php

namespace App\Filament\Resources\WalletResource\Pages;

use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\WalletResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateWallet extends CreateRecord
{
    protected static string $resource = WalletResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $wallet = auth()
            ->user()
            ->createWallet([
                'name' => $data['name'],
                'amount' => 0,
                // 'slug' => $this->generateSlug($data['name']),
                // 'meta' => ['currency' =>  $data['meta']],
                'description' => strip_tags($data['description']),
            ])
            ->deposit($data['amount']);

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
}
