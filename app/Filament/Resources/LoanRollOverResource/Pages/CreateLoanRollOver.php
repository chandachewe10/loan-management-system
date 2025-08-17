<?php

namespace App\Filament\Resources\LoanRollOverResource\Pages;

use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Bavix\Wallet\Models\Wallet;
use App\Models\Loan;
use App\Models\Borrower;
use App\Models\ThirdParty;
use App\Filament\Resources\LoanRollOverResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Http;
use App\Notifications\LoanStatusNotification;

class CreateLoanRollOver extends CreateRecord
{
    protected static string $resource = LoanRollOverResource::class;

    protected function handleRecordCreation(array $data): Model
    {





        $loan = Loan::findOrFail($data['loan_id']);
        Log::info('Loan Details: ' . $loan);

        $wallet = Wallet::where('name', "=", $loan->from_this_account)->where(
            'organization_id',
            "=",
            auth()->user()->organization_id
        )->first();
        Log::info('Wallet Details: ' . $wallet);

        $loan->update([
            'loan_due_date' => $data['new_due_date']
        ]);

        $wallet->deposit($data['payments'], ['meta' => 'Loan repayment amount']);




        $borrower = Borrower::find($loan->borrower_id);
        $repaymentAmount = $data['payments'];
        $this->sendSmsNotification($borrower, $loan, $repaymentAmount);


        if (!is_null($borrower->email)) {
            $this->sendEmailNotification($borrower, $loan, $repaymentAmount);
        }


        Notification::make()
            ->success()
            ->title('Roll Over Done')
            ->body('The loan has been rolled over successfully.')
            ->persistent()
            ->send();
        $this->halt();
        return $loan;
    }



    protected function sendSmsNotification($borrower, $data, $repaymentAmount)
    {


        $bulk_sms_config = ThirdParty::withoutGlobalScope('org')
            ->where('name', 'SWIFT-SMS')
            ->latest()
            ->first();

        if (
            $bulk_sms_config && $bulk_sms_config->is_active == "Active" && isset($borrower->mobile)
            && isset($bulk_sms_config->base_uri) && isset($bulk_sms_config->endpoint) && isset($bulk_sms_config->token)
            && isset($bulk_sms_config->sender_id)
        ) {
            $url = ($bulk_sms_config->base_uri ?? '') . ($bulk_sms_config->endpoint ?? '');

            if ($url && $bulk_sms_config->token && $bulk_sms_config->sender_id) {
                $message = 'Hi ' . $borrower->first_name . ', We have received your Interest repayment of K' .
                    $repaymentAmount . '. Your loan due date has been rolled over to next month on ' .
                    $data->loan_due_date . '. Thank you for your payment.';
                $jsonData = [
                    "sender_id" => $bulk_sms_config->sender_id,
                    "numbers" => $borrower->mobile,
                    "message" => $message,
                ];


                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $bulk_sms_config->token,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                    ->timeout(300)
                    ->withBody(json_encode($jsonData), 'application/json')
                    ->get($url);
            }
        }
    }

    protected function sendEmailNotification($borrower, $data, $repaymentAmount)
    {
        $message = 'Hi ' . $borrower->first_name . ', We have received your Interest repayment of K' .
            $repaymentAmount . '. Your loan due date has been rolled over to next month on ' .
            $data->loan_due_date . '. Thank you for your payment.';
        $borrower->notify(new LoanStatusNotification($message));
    }




    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Roll Over Done')
            ->body('The loan has been rolled over successfully.');
    }
}
