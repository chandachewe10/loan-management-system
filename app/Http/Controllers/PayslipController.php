<?php

namespace App\Http\Controllers;

use App\Models\Payslip;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PayslipController extends Controller
{
    public function download(Request $request, Payslip $payslip)
    {
        // Check authorization - user must be from same organization
        if (auth()->check() && auth()->user()->organization_id !== $payslip->organization_id) {
            abort(403, 'Unauthorized access to payslip');
        }

        $employee = $payslip->employee;
        $payrollRun = $payslip->payrollRun;

        // Generate PDF
        $pdf = Pdf::loadView('pdfs.payslip', [
            'payslip' => $payslip,
            'employee' => $employee,
            'payrollRun' => $payrollRun,
        ]);

        // Save PDF if not already saved
        if (empty($payslip->payslip_file_path)) {
            $pdfPath = storage_path('app/payslips/' . $payslip->payslip_number . '.pdf');
            $pdfDirectory = dirname($pdfPath);
            
            if (!file_exists($pdfDirectory)) {
                mkdir($pdfDirectory, 0755, true);
            }

            $pdf->save($pdfPath);

            $payslip->update([
                'payslip_file_path' => 'payslips/' . $payslip->payslip_number . '.pdf',
            ]);
        }

        return $pdf->download('payslip-' . $payslip->payslip_number . '.pdf');
    }
}

