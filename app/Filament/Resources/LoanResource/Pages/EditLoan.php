<?php

namespace App\Filament\Resources\LoanResource\Pages;

use App\Models\ThirdParty;
use Illuminate\Support\Facades\Http;
use App\Filament\Resources\LoanResource;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Bavix\Wallet\Models\Wallet;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Filament\Resources\Pages\CreateRecord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use Carbon\Carbon;
use App\Notifications\LoanStatusNotification;

class EditLoan extends EditRecord
{
    protected static string $resource = LoanResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\ViewAction::make(), Actions\DeleteAction::make()];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $loan = \App\Models\Loan::where('loan_number', '=', $data['loan_number'])->first();

        //Disable editing an already approved loan
        if ($loan->loan_status == 'approved') {
            Notification::make()->warning()->title('Loan already approved')->body('You cant edit an already approved loan')->persistent()->send();
            $this->halt();
        }

        $wallet = Wallet::where('name', '=', $data['from_this_account'])->first();
        // Check if the loan is being approved and they want to compile the Loan Agreement Form
        if ($data['loan_status'] === 'approved') {
            //Check if they have the Loan Agreement Form template for this type of loan
            $loan_agreement_text = \App\Models\LoanAgreementForms::where('loan_type_id', '=', $data['loan_type_id'])->first();

            if (!$loan_agreement_text && $data['activate_loan_agreement_form'] == 1) {
                Notification::make()
                    ->warning()
                    ->title('Invalid Agreement Form!')
                    ->body('Please create a template first if you want to compile the Loan Agreement Form')
                    ->persistent()
                    ->actions([Action::make('create')->button()->url(route('filament.admin.resources.loan-agreement-forms.create'), shouldOpenInNewTab: true)])
                    ->send();

                $this->halt();
            } else {
                //  $data['loan_number'] = IdGenerator::generate(['table' => 'loans', 'field' => 'loan_number', 'length' => 10, 'prefix' => 'LN-']);

                $loan_cycle = \App\Models\LoanType::findOrFail($data['loan_type_id'])->interest_cycle;

                $loan_duration = $data['loan_duration'];
                $loan_release_date = $data['loan_release_date'];
                $loan_date = Carbon::createFromFormat('Y-m-d', $loan_release_date);

                if ($loan_cycle === 'day(s)') {
                    $data['loan_due_date'] = $loan_date->addDays($loan_duration);
                }
                if ($loan_cycle === 'week(s)') {
                    $data['loan_due_date'] = $loan_date->addWeeks($loan_duration);
                }
                if ($loan_cycle === 'month(s)') {
                    $data['loan_due_date'] = $loan_date->addMonths($loan_duration);
                }
                if ($loan_cycle === 'year(s)') {
                    $data['loan_due_date'] = $loan_date->addYears($loan_duration);
                }

                $borrower = \App\Models\Borrower::findOrFail($data['borrower_id']);
                $loan_type = \App\Models\LoanType::findOrFail($data['loan_type_id']);

                $company_name = env('APP_NAME');
                $borrower_name = $borrower->first_name . ' ' . $borrower->last_name;
                $borrower_email = $borrower->email ?? '';
                $borrower_phone = $borrower->mobile ?? '';
                $loan_name = $loan_type->loan_name;
                $loan_interest_rate = $data['interest_rate'];
                $loan_amount = $data['principal_amount'];
                $loan_duration = $data['loan_duration'];
                $loan_release_date = $data['loan_release_date'];
                $loan_repayment_amount = $data['repayment_amount'];
                $loan_interest_amount = $data['interest_amount'];
                $loan_due_date = $data['loan_due_date'];
                $loan_number = $data['loan_number'];
                // The original content with placeholders
                $template_content = $loan_agreement_text->loan_agreement_text;

                // Replace placeholders with actual data
                $template_content = str_replace('[Company Name]', $company_name, $template_content);
                $template_content = str_replace('[Borrower Name]', strtoupper($borrower_name), $template_content);
                $template_content = str_replace('[Loan Tenure]', $loan_duration, $template_content);
                $template_content = str_replace('[Loan Interest Percentage]', $loan_interest_rate, $template_content);
                $template_content = str_replace('[Loan Interest Fee]', $loan_interest_amount, $template_content);
                $template_content = str_replace('[Loan Amount]', $loan_amount, $template_content);
                $template_content = str_replace('[Loan Repayments Amount]', $loan_repayment_amount, $template_content);
                $template_content = str_replace('[Loan Due Date]', $loan_due_date, $template_content);
                $template_content = str_replace('[Borrower Email]', $borrower_email, $template_content);
                $template_content = str_replace('[Borrower Phone]', $borrower_phone, $template_content);
                $template_content = str_replace('[Loan Name]', $loan_name, $template_content);
                $template_content = str_replace('[Loan Number]', $loan_number, $template_content);
                $template_content = str_replace('[Loan Release Date]', $loan_release_date, $template_content);

                $characters_to_remove = ['<br>', '&nbsp;'];
                $template_content = str_replace($characters_to_remove, '', $template_content);
                // Create a new PhpWord instance
                $phpWord = new PhpWord();

                // dd($template_content);
                // Add content to the document (agenda, summary, key points, sentiments)
                $section = $phpWord->addSection();

                // // Add an image to the document
                // $imagePath = public_path('Logos/logo2.png'); // Adjust the path to your image
                // $section->addImage($imagePath, [
                //     'width' => 170, // Adjust the width as needed 150
                //     'height' => 70, // Adjust the height as needed 50
                //     'align' => 'center' // Center align the image
                // ]);

                // // A TextRun object for applying formatting
                // $textRun = $section->addTextRun([
                //     'lineHeight' => 1.5 // Line height as a percentage (150% for 1.5 spacing)
                // ]);

                // // Add formatted text to the TextRun object
                // $textRun->addText($template_content, ['name' => 'Arial', 'size' => 12]);

                // \PhpOffice\PhpWord\Shared\Html::addHtml($section, $template_content);
                \PhpOffice\PhpWord\Shared\Html::addHtml($section, $template_content, false, false);

                // dd($template_content);
                // Agenda (bold, centered, uppercase, font size 14)
                // $section->addText($template_content, ['alignment' => 'center']);

                // Save the document as a Word file

                $current_year = date('Y');
                $path = public_path('LOAN_AGREEMENT_FORMS/' . $current_year . '/DOCX');
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                $file_name = Str::random(40) . '.docx';

                $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
                $objWriter->save($path . '/' . $file_name);
                $data['loan_agreement_file_path'] = 'LOAN_AGREEMENT_FORMS/' . $current_year . '/DOCX' . '/' . $file_name;
            }
        }

        // Send an SMS to the Client depending on the status of the Loan Stage

        $bulk_sms_config = ThirdParty::where('name', '=', 'SWIFT-SMS')->latest()->get()->first();
        $borrower = \App\Models\Borrower::findOrFail($data['borrower_id']);
        $base_uri = $bulk_sms_config->base_uri ?? '';
        $end_point = $bulk_sms_config->endpoint ?? '';

        if ($bulk_sms_config && $bulk_sms_config->is_active == 1 && isset($borrower->mobile) && isset($base_uri) && isset($end_point) && isset($bulk_sms_config->token) && isset($bulk_sms_config->sender_id)) {
            // Define the JSON data
            $url = $base_uri . $end_point;
            $message = 'Hi ' . $borrower->first_name . ', ';
            $loan_amount = $data['principal_amount'];
            $loan_duration = $data['loan_duration'];
            $loan_release_date = $data['loan_release_date'];
            $loan_repayment_amount = $data['repayment_amount'];
            $loan_interest_amount = $data['interest_amount'];
            $loan_due_date = $data['loan_due_date'] ?? '';
            $loan_number = $data['loan_number'] ?? '';

            // Assuming $data['loan_status'] contains the current status
            $loanStatus = $data['loan_status'];

            switch ($loanStatus) {
                case 'approved':
                    $message .= 'Congratulations! Your loan application of K' . $loan_amount . ' has been approved successfully. The total repayment amount is K' . $loan_repayment_amount . ' to be repaid in ' . $loan_duration . ' ' . $loan_cycle;
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
                'sender_id' => $bulk_sms_config->sender_id,
                'numbers' => $borrower->mobile,
                'message' => $message,
            ];

            // Convert the data to JSON format
            $jsonDataPayments = json_encode($jsonDataPayments);

            Http::withHeaders([
                'Authorization' => 'Bearer ' . $bulk_sms_config->token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
                ->timeout(300)
                ->withBody($jsonDataPayments, 'application/json')
                ->get($url);
        }

        // send via Email too if email is not Null
        if (!is_null($borrower->email)) {
            //dd('email is not null');
            $message = 'Hi ' . $borrower->first_name . ', ';
            $loan_amount = $data['principal_amount'];
            $loan_duration = $data['loan_duration'];
            $loan_release_date = $data['loan_release_date'];
            $loan_repayment_amount = $data['repayment_amount'];
            $loan_interest_amount = $data['interest_amount'];
            $loan_due_date = $data['loan_due_date'];
            $loan_number = $data['loan_number'];

            // Assuming $data['loan_status'] contains the current status
            $loanStatus = $data['loan_status'];

            switch ($loanStatus) {
                case 'approved':
                    $message .= 'Congratulations! Your loan application of K' . $loan_amount . ' has been approved successfully. The total repayment amount is K' . $loan_repayment_amount . ' to be repaid in ' . $loan_duration . ' ' . $loan_cycle;
                    break;

                case 'processing':
                    $message .= 'Your loan application of K' . $loan_amount . ' is currently under review. We will notify you once the review process is complete.';
                    break;

                case 'denied':
                    $message .= 'We regret to inform you that your loan application of K' . $loan_amount . ' has been rejected.';
                    break;

                case 'defaulted':
                    $message .= 'Unfortunately, your loan is in default status. Please contact us as soon as possible to discuss the situation.';
                    break;

                default:
                    $message .= 'Your loan application of K' . $loan_amount . ' is in progress. Current status: ' . $loanStatus;
                    break;
            }

            $borrower->notify(new LoanStatusNotification($message));
        }

        if ($data['loan_status'] === 'approved') {
            $wallet->withdraw($data['principal_amount'], ['meta' => 'Loan amount disbursed from ' . $data['from_this_account']]);
        }

        $record->update($data);

        return $record;
    }
}
