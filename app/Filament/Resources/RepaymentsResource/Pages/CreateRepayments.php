<?php

namespace App\Filament\Resources\RepaymentsResource\Pages;

use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Bavix\Wallet\Models\Wallet;
use App\Models\Expense;
use App\Models\Borrower;
use App\Filament\Resources\RepaymentsResource;
use App\Models\Repayments;
use Illuminate\Support\Str;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use App\Models\Loan;
use App\Notifications\LoanStatusNotification;
use App\Models\ThirdParty;
use Illuminate\Support\Facades\Http;
use App\Models\LedgerEntry;



class CreateRepayments extends CreateRecord
{
    protected static string $resource = RepaymentsResource::class;
    protected function handleRecordCreation(array $data): Model
    {

        //Check if they have created the Loan settlement Form template
        $template_content = \App\Models\LoanSettlementForms::latest()->first();
        if (!$template_content) {
            Notification::make()
                ->warning()
                ->title('Invalid Settlement Form!')
                ->body('Please create a loan settlement form first')
                ->persistent()
                ->actions([
                    Action::make('create')
                        ->button()
                        ->url(route('filament.admin.resources.loan-settlement-forms.create'), shouldOpenInNewTab: true),
                ])
                ->send();

            $this->halt();
        }


        $loan = Loan::findOrFail($data['loan_id']);
        Log::info('Loan Details: ' . $loan);

        $wallet = Wallet::where('name', "=", $loan->from_this_account)->where('organization_id',"=",
        auth()->user()->organization_id)->first();
        Log::info('Wallet Details: ' . $wallet);
        $principal_amount = $loan->principal_amount;
        $loan_number = $loan->loan_number;
        $old_balance = (float) ($loan->balance);
        $new_balance = ($old_balance) - ((float) ($data['payments']));

        $repayment = Repayments::create([
            'loan_id' => $data['loan_id'],
            'payments' => $data['payments'],
            'balance' => $new_balance,
            'payments_method' => $data['payments_method'],
            'reference_number' => $data['reference_number'] ?? 'No reference Was Entered by '.auth()->user()->name .' - '. auth()->user()->email,
            'loan_number' => $loan_number,
            'principal' => $principal_amount,

        ]);



        $wallet->deposit($data['payments'], ['meta' => 'Loan repayment amount']);
        LedgerEntry::create([
            'wallet_id' => $wallet->id,
          //  'transaction_id' => mt_rand(100000, 999999),
            'credit' => $data['payments'],
     ]);

        if ($new_balance <= 0) {
            $data['loan_settlement_file_path'] = $this->settlement_form($loan);

            //update Balance in Loans Table
            $loan->update([
                'balance' => $new_balance,
                'loan_status' => 'fully_paid',
                'loan_settlement_file_path' => $data['loan_settlement_file_path']
            ]);
        } else {
            $loan->update([
                'balance' => $new_balance,
                'loan_status' => 'partially_paid',

            ]);
        }

          $borrower = Borrower::find($loan->borrower_id);
          $repaymentAmount = $data['payments'];
          $this->sendSmsNotification($borrower, $loan,$repaymentAmount);

        // // Send email if available
        if (!is_null($borrower->email)) {
            $this->sendEmailNotification($borrower, $loan,$repaymentAmount);
        }

        return $repayment;
    }


    protected function settlement_form($loan)
    {
        $borrower = \App\Models\Borrower::findOrFail($loan->borrower_id);


        $company_name = auth()->user()->name;
        $company_address = 'Lusaka Zambia';
        $borrower_name = $borrower->first_name . ' ' . $borrower->last_name;
        $borrower_phone = $borrower->mobile ?? '';
        $loan_amount = $loan->repayment_amount;
        $settled_date = date('d, F Y');
        $current_date = date('d, F Y');


        // The original content with placeholders
        $template_content = \App\Models\LoanSettlementForms::latest()->first()->loan_settlement_text;


        // Replace placeholders with actual data
        $template_content = str_replace('{company_name}', $company_name, $template_content);
        $template_content = str_replace('{company_address}', $company_address, $template_content);
        $template_content = str_replace('{customer_name}', $borrower_name, $template_content);
        $template_content = str_replace('{customer_address}', $borrower_phone, $template_content);
        $template_content = str_replace('{loan_amount}', $loan_amount, $template_content);
        $template_content = str_replace('{settled_date}', $settled_date, $template_content);
        $template_content = str_replace('{current_date}', $current_date, $template_content);


        $characters_to_remove = ['<br>', '&nbsp;'];
        $template_content = str_replace($characters_to_remove, '', $template_content);
        // Create a new PhpWord instance
        $phpWord = new PhpWord();

        // dd($template_content);
        // Add content to the document (agenda, summary, key points, sentiments)
        $section = $phpWord->addSection();

        \PhpOffice\PhpWord\Shared\Html::addHtml($section, $template_content, false, false);

        $current_year = date('Y');
        $path = public_path('LOAN_SETTLEMENT_FORMS/' . $current_year . '/DOCX');
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        $file_name = Str::random(40) . '.docx';

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($path . '/' . $file_name);
        $path = 'LOAN_SETTLEMENT_FORMS/' . $current_year . '/DOCX' . '/' . $file_name;
        return $path;
    }


protected function sendSmsNotification($borrower, $data,$repaymentAmount)
    {


       $bulk_sms_config = ThirdParty::withoutGlobalScope('org')
       ->where('name', 'SWIFT-SMS')
       ->latest()
      ->first();

        if(
            $bulk_sms_config && $bulk_sms_config->is_active == "Active" && isset($borrower->mobile)
            && isset($bulk_sms_config->base_uri) && isset($bulk_sms_config->endpoint) && isset($bulk_sms_config->token)
            && isset($bulk_sms_config->sender_id)
        ) {
            $url = ($bulk_sms_config->base_uri ?? '') . ($bulk_sms_config->endpoint ?? '');

            if ($url && $bulk_sms_config->token && $bulk_sms_config->sender_id) {
                 $message = 'Hi ' . $borrower->first_name . ', We have received your repayment of K' .
                 $repaymentAmount . '. Your updated balance is K' .
                 $data->balance . '. Thank you for your payment.';
                $jsonData = [
                    "sender_id" => $bulk_sms_config->sender_id,
                    "numbers" => $borrower->mobile,
                    "message" => $message,
                ];


                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $bulk_sms_config->token,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',                ])
                ->timeout(300)
                ->withBody(json_encode($jsonData), 'application/json')
                ->get($url);


            }
        }


}

    protected function sendEmailNotification($borrower, $data,$repaymentAmount)
    {
         $message = 'Hi ' . $borrower->first_name . ', We have received your repayment of K' .
         $repaymentAmount . '. Your updated balance is K' .
         $data->balance . '. Thank you for your payment.';

         $borrower->notify(new LoanStatusNotification($message));
    }


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }


    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Repayment Done')
            ->body('The repayment has been updated successfully.');
    }




}
