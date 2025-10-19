<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Loan;
use App\Models\Borrower;
use App\Models\ThirdParty;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LoanMonitoringCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loans:monitor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor loans for defaults, due dates, and send automated SMS notifications to borrowers.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting loan monitoring...');
        
        // 1. Check for defaulted loans
        $defaultedCount = $this->checkDefaultedLoans();
        
        // 2. Send due date reminders
        $reminderCount = $this->sendDueDateReminders();
        
        // 3. Send default notifications
        $defaultNotificationCount = $this->sendDefaultNotifications();
        
        $this->info("Loan monitoring completed. Defaulted: {$defaultedCount}, Reminders: {$reminderCount}, Default Notifications: {$defaultNotificationCount}");
        
        return Command::SUCCESS;
    }

    /**
     * Check and update defaulted loans
     */
    private function checkDefaultedLoans(): int
    {
        $this->info('Checking for defaulted loans...');
        
        // Only check loans that are NOT already defaulted but should be
        $defaultedLoans = Loan::whereIn('loan_status', ['approved', 'partially_paid','defaulted'])
            ->where('loan_due_date', '<', now())
            ->where('balance', '>', 0)
            ->get();

        $count = 0;
        
        foreach ($defaultedLoans as $loan) {
            $daysOverdue = now()->diffInDays($loan->loan_due_date);
            
            $loan->update([
                'loan_status' => 'defaulted'
            ]);
            
            Log::warning("Loan {$loan->loan_number} marked as defaulted. Days overdue: {$daysOverdue}");
            $count++;
        }

        $this->info("Updated {$count} loans to defaulted status.");
        return $count;
    }

    /**
     * Send SMS reminders for upcoming due dates
     */
    private function sendDueDateReminders(): int
{
    $this->info('Sending due date reminders...');
    
    // Use DATE comparison instead of DATETIME
    $upcomingLoans = Loan::whereIn('loan_status', ['approved', 'partially_paid'])
        ->where('balance', '>', 0)
        ->whereDate('loan_due_date', '>=', now()->toDateString()) // Today or future
        ->whereDate('loan_due_date', '<=', now()->addDays(3)->toDateString()) // Next 3 days
        ->with('borrower')
        ->get();

    // Debug info
    $this->info("DEBUG - Current date: " . now()->toDateString());
    $this->info("DEBUG - 3 days from now: " . now()->addDays(3)->toDateString());
    $this->info("DEBUG - Loans found: " . $upcomingLoans->count());
    
    foreach ($upcomingLoans as $loan) {
        $daysUntilDue = now()->diffInDays($loan->loan_due_date, false);
        $this->info("DEBUG - Loan {$loan->loan_number} due: {$loan->loan_due_date} (in {$daysUntilDue} days)");
    }

    $sentCount = 0;
    
    foreach ($upcomingLoans as $loan) {
        if ($this->sendSMS($loan->borrower, $loan, 'due_reminder')) {
            $sentCount++;
            Log::info("Due date reminder sent for loan {$loan->loan_number}");
        }
    }

    $this->info("Sent {$sentCount} due date reminders.");
    return $sentCount;
}
    /**
     * Send notifications for defaulted loans
     */
    private function sendDefaultNotifications(): int
    {
        $this->info('Sending default notifications...');
        
        // Get loans that were defaulted today (to avoid spamming)
        $todayDefaults = Loan::where('loan_status', 'defaulted')
            ->whereDate('updated_at', today()) // Only loans marked defaulted today
            ->with('borrower')
            ->get();

        $sentCount = 0;
        
        foreach ($todayDefaults as $loan) {
            if ($this->sendSMS($loan->borrower, $loan, 'defaulted')) {
                $sentCount++;
                Log::warning("Default notification sent for loan {$loan->loan_number}");
            }
        }

        $this->info("Sent {$sentCount} default notifications.");
        return $sentCount;
    }

    /**
     * Send SMS based on loan status and type
     */
    private function sendSMS(Borrower $borrower, Loan $loan, string $messageType): bool
{
    $this->info("DEBUG - Attempting to send SMS for loan {$loan->loan_number}");
    
    $bulk_sms_config = ThirdParty::withoutGlobalScope('org')
        ->where('name', 'SWIFT-SMS')
        ->where('is_active', 1)
        ->latest()
        ->first();

    if (!$bulk_sms_config) {
        $this->error("DEBUG - No active SWIFT-SMS configuration found");
        return false;
    }

    if (!isset($borrower->mobile)) {
        $this->error("DEBUG - No mobile number for borrower {$borrower->id}");
        return false;
    }

    $this->info("DEBUG - Borrower mobile: {$borrower->mobile}");
    $this->info("DEBUG - SMS Config: " . ($bulk_sms_config->is_active ? 'Active' : 'Inactive'));

    $base_uri = $bulk_sms_config->base_uri ?? '';
    $end_point = $bulk_sms_config->endpoint ?? '';
    
    if (empty($base_uri) || empty($end_point)) {
        $this->error("DEBUG - Missing base_uri or endpoint");
        return false;
    }

    if (empty($bulk_sms_config->token) || empty($bulk_sms_config->sender_id)) {
        $this->error("DEBUG - Missing token or sender_id");
        return false;
    }

    $message = $this->buildMessage($borrower, $loan, $messageType);
    
    if (empty($message)) {
        $this->error("DEBUG - Empty message generated");
        return false;
    }

    $this->info("DEBUG - Message: {$message}");

    $jsonData = [
        "sender_id" => $bulk_sms_config->sender_id,
        "numbers" => $borrower->mobile,
        "message" => $message,
    ];

    try {
        $this->info("DEBUG - Sending request to: " . $base_uri . $end_point);
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $bulk_sms_config->token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])
        ->timeout(30)
        ->get($base_uri . $end_point, $jsonData);

        $this->info("DEBUG - Response status: " . $response->status());
        $this->info("DEBUG - Response body: " . $response->body());

        return $response->successful();
        
    } catch (\Exception $e) {
        $this->error("DEBUG - SMS sending failed: " . $e->getMessage());
        Log::error("SMS sending failed for loan {$loan->loan_number}: " . $e->getMessage());
        return false;
    }
}

    /**
     * Build appropriate message based on type
     */
    private function buildMessage(Borrower $borrower, Loan $loan, string $messageType): string
    {
        $baseMessage = 'Hi ' . $borrower->first_name . ', ';

        switch ($messageType) {
            case 'due_reminder':
                $daysUntilDue = now()->diffInDays($loan->loan_due_date, false);
                
                if ($daysUntilDue === 0) {
                    return $baseMessage . "Your loan {$loan->loan_number} is due TODAY! Amount due: ZMW " . number_format($loan->balance, 2) . ". Please make payment to avoid penalties.";
                } elseif ($daysUntilDue > 0) {
                    return $baseMessage . "Reminder: Your loan {$loan->loan_number} of ZMW " . number_format($loan->principal_amount, 2) . " is due in {$daysUntilDue} day(s). Outstanding balance: ZMW " . number_format($loan->balance, 2) . ".";
                }
                break;

            case 'defaulted':
                $daysOverdue = now()->diffInDays($loan->loan_due_date);
                return $baseMessage . "URGENT: Your loan {$loan->loan_number} is {$daysOverdue} day(s) overdue! Outstanding: ZMW " . number_format($loan->balance, 2) . ". Contact us immediately to avoid further action.";

            default:
                return '';
        }

        return '';
    }
}