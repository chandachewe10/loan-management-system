<?php

namespace App\Notifications;

use App\Models\Payslip;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;

class PayslipNotification extends Notification
{
    use Queueable;

    protected $payslip;

    /**
     * Create a new notification instance.
     */
    public function __construct(Payslip $payslip)
    {
        $this->payslip = $payslip;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $employee = $this->payslip->employee;
        $payrollRun = $this->payslip->payrollRun;

        // Generate PDF
        $pdf = Pdf::loadView('pdfs.payslip', [
            'payslip' => $this->payslip,
            'employee' => $employee,
            'payrollRun' => $payrollRun,
        ]);

        $pdfPath = storage_path('app/payslips/' . $this->payslip->payslip_number . '.pdf');
        $pdfDirectory = dirname($pdfPath);
        
        if (!file_exists($pdfDirectory)) {
            mkdir($pdfDirectory, 0755, true);
        }

        $pdf->save($pdfPath);

        // Update payslip record
        $this->payslip->update([
            'payslip_sent' => true,
            'payslip_sent_at' => now(),
            'payslip_file_path' => 'payslips/' . $this->payslip->payslip_number . '.pdf',
        ]);

        return (new MailMessage)
            ->subject('Your Payslip - ' . $payrollRun->period_name)
            ->greeting('Hello ' . $employee->first_name . ',')
            ->line('Please find attached your payslip for ' . $payrollRun->period_name . '.')
            ->line('Payment Date: ' . $payrollRun->payment_date->format('F d, Y'))
            ->line('Net Pay: ZMW ' . number_format($this->payslip->net_pay, 2))
            ->line('If you have any questions, please contact your HR department.')
            ->attach($pdfPath, [
                'as' => 'payslip-' . $this->payslip->payslip_number . '.pdf',
                'mime' => 'application/pdf',
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

    /**
     * Static method to send payslip
     */
    public static function send(Payslip $payslip): void
    {
        $employee = $payslip->employee;
        $employee->notify(new self($payslip));
    }
}

