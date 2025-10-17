<?php

namespace App\Filament\Resources\AssetCategoryResource\Pages;

use App\Filament\Resources\AssetCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAssetCategory extends CreateRecord
{
    protected static string $resource = AssetCategoryResource::class;

     protected function getRedirectUrl(): string
    {

        return $this->getResource()::getUrl('index');
    }
}
