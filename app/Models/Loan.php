<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;


class Loan extends Model
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

    public function borrower()
    {
        
        return $this->belongsTo(Borrower::class, 'borrower_id','id');
    }

    public function getLoanDueDateAttribute($value) {
        return date('d,F Y', strtotime($value));
    }


    protected $casts = [
        'activate_loan_agreement_form' => 'boolean',
    ];
     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'balance',
        'loan_status',
    ];


}
