<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Builder;


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


    protected static function booted(): void
    {
       
        static::addGlobalScope('org', function (Builder $query) {
          
            if (auth()->check()) {
                
                $query->where('organization_id', auth()->user()->organization_id);
            }
        });
    }

}
