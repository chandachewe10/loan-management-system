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
use Carbon\Carbon;

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
                ->example('1000')
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
                ->example('2025HK092KSQ123')
                ->label('Reference Number')
                ->requiredMapping(),
        ];
    }

    public function resolveRecord(): ?Repayments
    {
        $loan = Loan::where('loan_number', $this->data['loan_number'])->first();

        if (!$loan) {
            Log::warning('Loan not found for loan number: ' . $this->data['loan_number']);
            return null;
        }

        Log::info('Loan Details: ' . $loan);

        $wallet = Wallet::where('name', $loan->from_this_account)
            ->where('organization_id', auth()->user()->organization_id)
            ->first();

        if (!$wallet) {
            Log::error('Wallet not found for loan: ' . $loan->loan_number);
            return null;
        }

        Log::info('Wallet Details: ' . $wallet);

        // Get payment details
        $payment_amount = (float) $this->data['payments'];
        $old_balance = (float) $loan->balance;
        $principal_amount = $loan->principal_amount;
        $loan_number = $this->data['loan_number'];

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

                Log::info('Early Repayment detected for loan ' . $loan_number . 
                    '. Applied ERS of ' . $early_repayment_percent . 
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
        if ($new_balance < 0) {
            Log::warning('Overpayment detected for loan ' . $loan_number . '. Balance would be negative: ' . $new_balance);
            $new_balance = 0;
        }

        // Create repayment record
        $repayment = Repayments::create([
            'loan_id' => $loan->id,
            'balance' => $new_balance,
            'payments' => $payment_amount,
            'principal' => $principal_amount,
            'payments_method' => $this->data['payments_method'],
            'reference_number' => $this->data['reference_number'] ?? Uuid::uuid4()->toString(),
            'loan_number' => $loan_number,
        ]);

        // Update wallet and create ledger entry
        $wallet->deposit($payment_amount, ['meta' => 'Loan repayment amount - Import']);



        // Update loan status
        if ($new_balance <= 0) {
            $settlement_file_path = $this->settlement_form($loan);

            $loan->update([
                'balance' => 0,
                'loan_status' => 'fully_paid',
                'loan_settlement_file_path' => $settlement_file_path
            ]);

            Log::info('Loan ' . $loan_number . ' fully paid via import. Settlement form generated.');
        } else {
            $loan->update([
                'balance' => $new_balance,
                'loan_status' => 'partially_paid',
            ]);

            Log::info('Loan ' . $loan_number . ' partially paid via import. New balance: ' . $new_balance);
        }

        // Send notifications
        $borrower = \App\Models\Borrower::find($loan->borrower_id);

        if ($borrower) {
            $this->sendSmsNotification($borrower, $loan, $payment_amount);

            if (!is_null($borrower->email)) {
                $this->sendEmailNotification($borrower, $loan, $payment_amount);
            }
        }

        return $repayment;
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

        // Get organization details
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

                    Log::info('SMS notification sent to ' . $borrower->mobile . ' for loan ' . $loan->loan_number);
                } catch (\Exception $e) {
                    Log::error('Failed to send SMS notification for loan ' . $loan->loan_number . ': ' . $e->getMessage());
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

            Log::info('Email notification sent to ' . $borrower->email . ' for loan ' . $loan->loan_number);
        } catch (\Exception $e) {
            Log::error('Failed to send email notification for loan ' . $loan->loan_number . ': ' . $e->getMessage());
        }
    }
}