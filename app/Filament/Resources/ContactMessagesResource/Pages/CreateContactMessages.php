<?php

namespace App\Filament\Resources\ContactMessagesResource\Pages;

use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\ContactMessagesResource;
use Filament\Actions;
use App\Models\Borrower as Contact;
use App\Models\Messages; 
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
        
        // Get the sender ID and message
        $senderId = 'MACROIT'; // Default sender ID, can be replaced with dynamic fetching
        $message = $data['message'];
       

      

      
        // Convert the array of contact strings into a comma-separated string
        $contactsString = implode(',', $contactStrings);
        
        // TODO Replace with SWIFTSMS APIS
        $encodedContacts = urlencode($contactsString);
        $encodedSenderId = urlencode($senderId);
        $encodedMessage = urlencode($message);
        
        // Construct the URL with properly encoded components
        $url = env('BULK_SMS_BASE_URI') . '/api_key/' . urlencode(env('BULK_SMS_TOKEN')) . '/contacts/' . $encodedContacts . '/senderId/' . $encodedSenderId . '/message/' . $encodedMessage;
        
        // Send the HTTP request
        $response = Http::timeout(300)->get($url);

        
        // Handle the response
        $responseData = $response->json();
       
        // Prepare data for message record creation
        $messageData = [
            'message' => $message,
            'responseText' => $responseData['responseText'] ?? '',
            'contact' => $contactsString,
            'status' => $response->status(),
           
        ];
        
   
        $data = Messages::create($messageData);

        if ($responseData['statusCode'] == 202) {
       
            
            Notification::make()
                ->title('Message(s) Sent')
                ->body($responseData['responseText'] ?? 'SMS(es) have been queued for delivery.')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Failed to Send Message(s)')
                ->body($responseData['responseText'] ?? 'There was an error sending the SMS(es).')
                ->danger()
                ->send();
        }
        
        
        return $data;
    }
        catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('An error occurred while sending the message: ' . $e->getMessage())
                ->danger()
                ->send();
            $this->halt(); 
           
            
        }
    }


      protected function getRedirectUrl(): string
    {
       
        return $this->getResource()::getUrl('index');
    }

}