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

use Carbon\Carbon;

class CreateRepayments extends CreateRecord
{
    protected static string $resource = RepaymentsResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Check if loan settlement form template exists
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

        // Load loan and wallet
        $loan = Loan::findOrFail($data['loan_id']);
        Log::info('Loan Details: ' . $loan);

        $wallet = Wallet::where('name', $loan->from_this_account)
            ->where('organization_id', auth()->user()->organization_id)
            ->first();
        Log::info('Wallet Details: ' . $wallet);

        // Get payment amount
        $payment_amount = (float) $data['payments'];
        $old_balance = (float) $loan->balance;
        $principal_amount = $loan->principal_amount;
        $loan_number = $loan->loan_number;

        // Initialize variables for early settlement
        $is_early_settlement = false;
        $early_settlement_discount = 0;
        $adjusted_interest = $loan->interest_amount;

        // Check if this payment fully settles the loan
        $will_fully_settle = ($old_balance - $payment_amount) <= 0;

        // Apply early repayment settlement if applicable
        if ($will_fully_settle && $old_balance > 0 && Carbon::parse($loan->loan_due_date)->isFuture()) {
            $interest_amount = $loan->interest_amount;
            $early_repayment_percent = $loan->loan_type->early_repayment_percent ?? 0;

            if ($early_repayment_percent > 0 && $interest_amount > 0) {
                $is_early_settlement = true;
                $early_settlement_discount = ($interest_amount * $early_repayment_percent) / 100;
                $adjusted_interest = $interest_amount - $early_settlement_discount;

                // Update loan with new interest amount
                $loan->interest_amount = $adjusted_interest;
                $loan->is_early_settlement = 1;
                $loan->save();

                Log::info('Early Repayment detected. Applied ERS of ' . $early_repayment_percent . 
                    '%. Interest reduced from ' . $interest_amount . ' to ' . $adjusted_interest);
            }
        }

        // Calculate new balance (accounting for any early settlement discount)
        if ($is_early_settlement) {
            // Recalculate total loan amount with reduced interest
            $adjusted_loan_total = $principal_amount + $adjusted_interest;
            $new_balance = $adjusted_loan_total - $payment_amount;
        } else {
            $new_balance = $old_balance - $payment_amount;
        }

        // Handle overpayment
        // if ($new_balance < 0) {
        //     Log::warning('Overpayment detected. Balance would be negative: ' . $new_balance);
        //     $new_balance = 0;
        // }

        // Create repayment record
        $repayment = Repayments::create([
            'loan_id' => $data['loan_id'],
            'payments' => $payment_amount,
            'balance' => $new_balance,
            'payments_method' => $data['payments_method'],
            'reference_number' => $data['reference_number'] ?? 
            'No reference was entered by ' . auth()->user()->name . ' - ' . auth()->user()->email,
            'loan_number' => $loan_number,
            'principal' => $principal_amount,
        ]);

        // Update wallet and create ledger entry
        $wallet->deposit($payment_amount, ['meta' => 'Loan repayment amount']);
        
    

        // Update loan status
        if ($new_balance <= 0) {
            $settlement_file_path = $this->settlement_form($loan);

            $loan->update([
                'balance' => 0,
                'loan_status' => 'fully_paid',
                'loan_settlement_file_path' => $settlement_file_path
            ]);

            Log::info('Loan ' . $loan_number . ' fully paid. Settlement form generated.');
        } else {
            $loan->update([
                'balance' => $new_balance,
                'loan_status' => 'partially_paid',
            ]);

            Log::info('Loan ' . $loan_number . ' partially paid. New balance: ' . $new_balance);
        }

        // Send notifications
        $borrower = Borrower::find($loan->borrower_id);
        
        if ($borrower) {
            $this->sendSmsNotification($borrower, $loan, $payment_amount);

            if (!is_null($borrower->email)) {
                $this->sendEmailNotification($borrower, $loan, $payment_amount);
            }
        }

        return $repayment;
    }

    protected function settlement_form($loan)
    {
        $borrower = \App\Models\Borrower::findOrFail($loan->borrower_id);

        // Get organization details (consider storing in config or organization model)
        $company_name = auth()->user()->organization->name ?? auth()->user()->name;
        $company_address = auth()->user()->organization->address ?? 'Lusaka, Zambia';
        $borrower_name = $borrower->first_name . ' ' . $borrower->last_name;
        $borrower_phone = $borrower->mobile ?? '';
        $loan_amount = $loan->repayment_amount;
        $settled_date = date('d F Y');
        $current_date = date('d F Y');

        // Get template content
        $template_content = \App\Models\LoanSettlementForms::latest()->first()->loan_settlement_text;

        // Replace placeholders with actual data
        $replacements = [
            '{company_name}' => $company_name,
            '{company_address}' => $company_address,
            '{customer_name}' => $borrower_name,
            '{customer_address}' => $borrower_phone,
            '{loan_amount}' => $loan_amount,
            '{settled_date}' => $settled_date,
            '{current_date}' => $current_date,
        ];

        $template_content = str_replace(array_keys($replacements), array_values($replacements), $template_content);

        // Clean up HTML entities
        $characters_to_remove = ['<br>', '&nbsp;'];
        $template_content = str_replace($characters_to_remove, '', $template_content);

        // Create PhpWord document
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        \PhpOffice\PhpWord\Shared\Html::addHtml($section, $template_content, false, false);

        // Save document
        $current_year = date('Y');
        $path = public_path('LOAN_SETTLEMENT_FORMS/' . $current_year . '/DOCX');
        
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $file_name = Str::random(40) . '.docx';
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($path . '/' . $file_name);

        return 'LOAN_SETTLEMENT_FORMS/' . $current_year . '/DOCX/' . $file_name;
    }

    protected function sendSmsNotification($borrower, $loan, $repaymentAmount)
    {
        $bulk_sms_config = ThirdParty::withoutGlobalScope('org')
            ->where('name', 'SWIFT-SMS')
            ->latest()
            ->first();

        if (
            $bulk_sms_config && 
            $bulk_sms_config->is_active == "Active" && 
            isset($borrower->mobile) &&
            isset($bulk_sms_config->base_uri) && 
            isset($bulk_sms_config->endpoint) && 
            isset($bulk_sms_config->token) &&
            isset($bulk_sms_config->sender_id)
        ) {
            $url = $bulk_sms_config->base_uri . $bulk_sms_config->endpoint;

            if ($url && $bulk_sms_config->token && $bulk_sms_config->sender_id) {
                $message = 'Hi ' . $borrower->first_name . ', We have received your repayment of K' .
                    number_format($repaymentAmount, 2) . '. Your updated balance is K' .
                    number_format($loan->balance, 2) . '. Thank you for your payment.';

                $jsonData = [
                    "sender_id" => $bulk_sms_config->sender_id,
                    "numbers" => $borrower->mobile,
                    "message" => $message,
                ];

                try {
                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $bulk_sms_config->token,
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ])
                        ->timeout(300)
                        ->withBody(json_encode($jsonData), 'application/json')
                        ->get($url);

                    Log::info('SMS notification sent to ' . $borrower->mobile);
                } catch (\Exception $e) {
                    Log::error('Failed to send SMS notification: ' . $e->getMessage());
                }
            }
        }
    }

    protected function sendEmailNotification($borrower, $loan, $repaymentAmount)
    {
        try {
            $message = 'Hi ' . $borrower->first_name . ', We have received your repayment of K' .
                number_format($repaymentAmount, 2) . '. Your updated balance is K' .
                number_format($loan->balance, 2) . '. Thank you for your payment.';

            $borrower->notify(new LoanStatusNotification($message));
            
            Log::info('Email notification sent to ' . $borrower->email);
        } catch (\Exception $e) {
            Log::error('Failed to send email notification: ' . $e->getMessage());
        }
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