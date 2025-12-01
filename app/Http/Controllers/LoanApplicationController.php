<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LoanApplicationController extends Controller
{
    public function preview($id)
    {
        $loan = Loan::with('borrower', 'loan_type')->findOrFail($id);
        
        $user = auth()->user();
        $companyName = $user->name ?? config('app.name');
        $branch = $user && $user->branch ? $user->branch : null;

        $pdf = Pdf::loadView('pdfs.loan-application', compact('loan', 'companyName', 'branch', 'user'));

        return $pdf->stream("loan-application-{$loan->id}.pdf");
    }

    public function download($id)
    {
        $loan = Loan::with('borrower', 'loan_type')->findOrFail($id);
        
        $user = auth()->user();
        $companyName = $user->name ?? config('app.name');
        $branch = $user && $user->branch ? $user->branch : null;

        $pdf = Pdf::loadView('pdfs.loan-application', compact('loan', 'companyName', 'branch', 'user'));

        return $pdf->download("loan-application-{$loan->id}.pdf");
    }
}

