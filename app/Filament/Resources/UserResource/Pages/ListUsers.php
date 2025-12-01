<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('companyProfile')
                ->label('Company Profile')
                ->icon('heroicon-m-building-office')
                ->color('primary')
                ->url('/admin/profile-completion')
                ->tooltip('Update your company profile information'),
            Actions\CreateAction::make(),
        ];
    }
}
