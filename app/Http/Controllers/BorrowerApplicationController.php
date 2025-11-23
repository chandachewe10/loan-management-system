<?php

namespace App\Http\Controllers;

use App\Models\Borrower;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class BorrowerApplicationController extends Controller
{
    public function preview($id)
    {
        $borrower = Borrower::findOrFail($id);
        
        $user = auth()->user();
        $companyName = $user->name ?? config('app.name');
        $branch = $user && $user->branch ? $user->branch : null;

        $pdf = Pdf::loadView('pdfs.borrower-application', compact('borrower', 'companyName', 'branch', 'user'));

        return $pdf->stream("borrower-application-{$borrower->id}.pdf");
    }

    public function download($id)
    {
        $borrower = Borrower::findOrFail($id);
        
        $user = auth()->user();
        $companyName = $user->name ?? config('app.name');
        $branch = $user && $user->branch ? $user->branch : null;

        $pdf = Pdf::loadView('pdfs.borrower-application', compact('borrower', 'companyName', 'branch', 'user'));

        return $pdf->download("borrower-application-{$borrower->id}.pdf");
    }
}

