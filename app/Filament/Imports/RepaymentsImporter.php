<?php

namespace App\Filament\Imports;

use App\Models\Repayments;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Bavix\Wallet\Models\Wallet;
use App\Models\Expense;
use App\Filament\Resources\RepaymentsResource;
use Illuminate\Support\Str;
use Filament\Actions;
use App\Models\Messages;
use App\Models\ThirdParty;
use Filament\Resources\Pages\CreateRecord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use App\Models\Loan;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Notifications\LoanStatusNotification;


class RepaymentsImporter extends Importer
{
    protected static ?string $model = Repayments::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('loan_number')
            ->example('LN-0000001')
            ->label('Loan Number')
            ->rules(['max:255']),

            ImportColumn::make('payments')
            ->example('1')
            ->label('Payment')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'numeric']),

            ImportColumn::make('payments_method')
            ->example('Mobile Money')
            ->label('Payment Method')
                ->requiredMapping()
                ->rules(['required', 'max:255']),

            ImportColumn::make('reference_number')
            ->example('2025091082882')
            ->label('Reference Number')
                ->requiredMapping(),




        ];
    }

    public function resolveRecord(): ?Repayments
    {
        // return Repayments::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        $loan = Loan::where('loan_number',"=",$this->data['loan_number'])->first();


        if($loan){
            Log::info('Loan Details: ' . $loan);
            $wallet = Wallet::findOrFail($loan->from_this_account);
            Log::info('Wallet Details: ' . $wallet);
            $principal_amount = $loan->principal_amount;
            $loan_number = $this->data['loan_number'];
            $old_balance = (float) ($loan->balance);
            $new_balance = ($old_balance) - ((float) ($this->data['payments']));


            $repayment = Repayments::create([

                'loan_id' => $loan->id,
                'balance' =>  $new_balance,
                'payments' => $this->data['payments'],
                'principal' => $principal_amount,
                'payments_method' => $this->data['payments_method'],
                'reference_number' => $this->data['reference_number'] ?? Uuid::uuid4()->toString(),
                'loan_number' => $loan_number,


     ]);

           $loan->update([
          'balance' => $new_balance,

]);










        if ($new_balance <= 0 ) {


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


        $wallet->deposit($this->data['payments'], ['meta' => 'Loan repayment amount']);

        $borrower = \App\Models\Borrower::find($loan->borrower_id);

        $repaymentAmount = $this->data['payments'];
         $this->sendSmsNotification($borrower, $loan,$repaymentAmount);

        // // Send email if available
        if (!is_null($borrower->email)) {
            $this->sendEmailNotification($borrower, $loan,$repaymentAmount);
        }



       return $repayment;
        }


     //   return new Repayments();

    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your repayments import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
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


        $bulk_sms_config = ThirdParty::where('name', "=", 'SWIFT-SMS')->latest()->first();

        if ($bulk_sms_config && $bulk_sms_config->is_active == "ACTIVE" && $borrower->mobile) {
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

                Http::withHeaders([
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





}
