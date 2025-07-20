<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class LoanAgreementForms extends Model
{
    use HasFactory;
    use LogsActivity;

public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logAll();
    }
    
    public function loan_type()
    {
        
        return $this->belongsTo(LoanType::class, 'loan_type_id','id');
    }

   
}
