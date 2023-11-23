<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    public function loan_type()
    {
        
        return $this->belongsTo(LoanType::class, 'loan_type_id','id');
    }

    public function borrower()
    {
        
        return $this->belongsTo(Borrower::class, 'borrower_id','id');
    }

}
