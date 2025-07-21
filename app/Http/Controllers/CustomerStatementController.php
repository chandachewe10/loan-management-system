<?php

namespace App\Http\Controllers;
use App\Models\Customer; 
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;


class CustomerStatementController extends Controller
{
    public function download($id)
    {

        $loan = \App\Models\Loan::findOrFail($id);
        $customer = \App\Models\Borrower::find($loan->borrower_id);

        $pdf = PDF::loadView('pdfs.customer-statement', compact('customer','loan'));

        return $pdf->download("statement-{$customer->id}.pdf");
    }


}
