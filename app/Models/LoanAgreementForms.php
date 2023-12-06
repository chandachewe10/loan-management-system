<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanAgreementForms extends Model
{
    use HasFactory;

    public function loan_type()
    {
        
        return $this->belongsTo(LoanType::class, 'loan_type_id','id');
    }

    public function getCreatedAtAttribute($value) {
        return date('d,F Y', strtotime($value));
    }

}
