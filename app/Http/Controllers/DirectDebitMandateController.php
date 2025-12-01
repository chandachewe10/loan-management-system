<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\DirectDebitMandateSettings;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DirectDebitMandateController extends Controller
{
    public function preview($id)
    {
        $loan = Loan::with('borrower', 'loan_type')->findOrFail($id);
        
        $user = auth()->user();
        $companyName = $user->name ?? config('app.name');
        $branch = $user && $user->branch ? $user->branch : null;
        
        // Get settings or use defaults
        $settings = DirectDebitMandateSettings::getSettings();

        $pdf = Pdf::loadView('pdfs.direct-debit-mandate', compact('loan', 'companyName', 'branch', 'user', 'settings'));

        return $pdf->stream("direct-debit-mandate-{$loan->id}.pdf");
    }

    public function download($id)
    {
        $loan = Loan::with('borrower', 'loan_type')->findOrFail($id);
        
        $user = auth()->user();
        $companyName = $user->name ?? config('app.name');
        $branch = $user && $user->branch ? $user->branch : null;
        
        // Get settings or use defaults
        $settings = DirectDebitMandateSettings::getSettings();

        $pdf = Pdf::loadView('pdfs.direct-debit-mandate', compact('loan', 'companyName', 'branch', 'user', 'settings'));

        return $pdf->download("direct-debit-mandate-{$loan->id}.pdf");
    }
}

