<?php

namespace App\Filament\Resources\BorrowerResource\Pages;

use App\Filament\Resources\BorrowerResource;
use Filament\Notifications\Notification;
use Auth;
use Filament\Resources\Pages\CreateRecord;


class CreateBorrower extends CreateRecord
{
    protected static string $resource = BorrowerResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        
       $data['full_name'] = $data['first_name']. ' '.$data['last_name']. ' - '.$data['mobile'];
       $data['added_by'] =Auth::user()->id;
       return $data;
    }


      protected function getRedirectUrl(): string
    {
        // Redirect to preview the application after creation
        return route('borrower.application.preview', ['id' => $this->record->id]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Customer created')
            ->body('The Customer has been created successfully. Previewing application form...')
            ->actions([
                \Filament\Notifications\Actions\Action::make('view')
                    ->label('View Borrower')
                    ->url($this->getResource()::getUrl('view', ['record' => $this->record]))
            ]);
    }


                                    
}
