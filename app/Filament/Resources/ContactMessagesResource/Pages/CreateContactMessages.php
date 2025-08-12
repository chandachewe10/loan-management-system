<?php

namespace App\Filament\Resources\ContactMessagesResource\Pages;

use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\ContactMessagesResource;
use Filament\Actions;
use App\Models\Borrower as Contact;
use App\Models\Messages;
use App\Models\ThirdParty;
use App\Models\SenderId;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Http;
use Filament\Notifications\Notification;

class CreateContactMessages extends CreateRecord
{
    protected static string $resource = ContactMessagesResource::class;

    protected function handleRecordCreation(array $data): Model
    {

        try{
        // Fetch contacts using findMany
        $ids = $data['contact'];
        $contacts = Contact::findMany($ids);

        $contactStrings = $contacts->map(function($contact) {
            return $contact->mobile;
        })->toArray();

        $message = $data['message'];





        // Convert the array of contact strings into a comma-separated string
        $contactsFromArray = implode(',', $contactStrings);


        $encodedContacts = urlencode($contactsFromArray);
        $encodedMessage = urlencode($message);
         $bulk_sms_config = ThirdParty::withoutGlobalScope('org')
       ->where('name', 'SWIFT-SMS')
       ->latest()
      ->first();


    $base_uri = $bulk_sms_config->base_uri ?? '';
    $end_point = $bulk_sms_config->endpoint ?? '';
    $responseData = null;

    if (
        $bulk_sms_config &&
        $bulk_sms_config->is_active == "Active" &&
        !empty($contactStrings) &&
        !empty($base_uri) &&
        !empty($end_point) &&
        !empty($bulk_sms_config->token) &&
        !empty($bulk_sms_config->sender_id)
    ) {

        $url = $base_uri . $end_point;

        $payload = [
            'sender_id' => $bulk_sms_config->sender_id,
            'numbers' => $encodedContacts,
            'message' => $encodedMessage,
        ];


            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $bulk_sms_config->token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
                ->timeout(30)
                ->get($url, $payload);

            $responseData = $response->json();




    $statusCode = $responseData['success'] ?? 500;

    $responseText = $responseData['message'] ?? 'Unknown error';

    $messageRecord = Messages::create([
        'message' => $message,
        'responseText' => $responseText,
        'contact' => $encodedContacts,
        'status' => $statusCode,

    ]);

    if ($statusCode == 'true') {
        Notification::make()
            ->title('Message(s) sent')
            ->body($responseText)
            ->success()
            ->send();
            return $messageRecord;

    } else {
        Notification::make()
            ->title('Failed to send message(s)')
            ->body($responseText)
            ->danger()
            ->send();
    }

    $this->halt();

    return $messageRecord;
}
        }

 catch (\Throwable $e) {
            $responseData = [
                'statusCode' => 500,
                'responseText' => $e->getMessage(),
            ];
        }


}




      protected function getRedirectUrl(): string
    {

        return $this->getResource()::getUrl('index');
    }

}
