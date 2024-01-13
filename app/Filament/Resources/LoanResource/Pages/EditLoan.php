<?php

namespace App\Filament\Resources\LoanResource\Pages;
use App\Models\ThirdParty;
use Illuminate\Support\Facades\Http;
use App\Filament\Resources\LoanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;


class EditLoan extends EditRecord
{
    protected static string $resource = LoanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
{
    // Send an SMS to the Client depending on the status of the Loan Stage

    $bulk_sms_config = ThirdParty::where('name', "=", 'SWIFT-SMS')->latest()->get()->first();
    $borrower = \App\Models\Borrower::findOrFail($data['borrower_id'])->first();
    $base_uri = $bulk_sms_config->base_uri ?? '';
    $end_point = $bulk_sms_config->endpoint ?? '';
     
    if (
        $bulk_sms_config && $bulk_sms_config->is_active === 'Active' && isset($borrower->mobile)
        && isset($base_uri) && isset($end_point) && isset($bulk_sms_config->token)
        && isset($bulk_sms_config->sender_id)
    ) {
      
        
        // Define the JSON data
        $url = $base_uri . $end_point;
        $message = 'Hi ' . $borrower->first_name . ', ';

        // Assuming $data['loan_status'] contains the current status
        $loanStatus = $data['loan_status'];

        switch ($loanStatus) {
            case 'approved':
                $message .= 'Congratulations! Your loan application has been approved.';
                break;

            case 'processing':
                $message .= 'Your loan application is currently under review. We will notify you once the review process is complete.';
                break;

            case 'denied':
                $message .= 'We regret to inform you that your loan application has been rejected.';
                break;

            case 'defaulted':
                $message .= 'Unfortunately, your loan is in default status. Please contact us as soon as possible to discuss the situation.';
                break;



            default:
                $message .= 'Your loan application is in progress. Current status: ' . $loanStatus;
                break;
        }


        $jsonDataPayments = [
            "sender_id" => $bulk_sms_config->sender_id,
            "numbers" => $borrower->mobile,
            "message" => $message,

        ];

        // Convert the data to JSON format
        $jsonDataPayments = json_encode($jsonDataPayments);

      $request = Http::withHeaders([
            'Authorization' => 'Bearer ' . $bulk_sms_config->token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])
            ->timeout(300)
            ->withBody($jsonDataPayments, 'application/json')
            ->get($url);

            ($request);
    }


    
    return $record;
}
}
