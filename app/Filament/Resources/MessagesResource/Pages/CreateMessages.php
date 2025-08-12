<?php
namespace App\Filament\Resources\MessagesResource\Pages;

use App\Filament\Resources\MessagesResource;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use App\Models\Messages;
use App\Models\ThirdParty;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Http;

class CreateMessages extends CreateRecord
{
    protected static string $resource = MessagesResource::class;

  protected function handleRecordCreation(array $data): Model
{
    $contacts = $data['contact'];
    $message = $data['message'];

    // Flatten and clean up contact list
    $contactStrings = array_map(function ($contact) {
        return is_array($contact) ? $contact['contact'] : $contact;
    }, $contacts);

    $contactsString = implode(',', $contactStrings);

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
            'numbers' => implode(',', $contactStrings),
            'message' => $message,
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $bulk_sms_config->token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
                ->timeout(30)
                ->get($url, $payload);

            $responseData = $response->json();


        } catch (\Throwable $e) {
            $responseData = [
                'statusCode' => 500,
                'responseText' => $e->getMessage(),
            ];
        }
    }

    $statusCode = $responseData['success'] ?? 500;

    $responseText = $responseData['message'] ?? 'Unknown error';

    $messageRecord = Messages::create([
        'message' => $message,
        'responseText' => $responseText,
        'contact' => $contactsString,
        'status' => $statusCode,

    ]);

    if ($statusCode == 'true') {
        Notification::make()
            ->title('Message(s) sent')
            ->body($responseText)
            ->success()
            ->send();
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


    protected function getRedirectUrl(): string
    {

        return $this->getResource()::getUrl('index');
    }


}
