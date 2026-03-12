<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Builder;

class Repayments extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'loan_id',
        'loan_number',
        'balance',
        'payments',
        'principal',
        'payments_method',
        'reference_number',
        'repayment_date',
        'organization_id',
        'branch_id',
    ];

    protected $casts = [
        'repayment_date' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll();
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class, 'loan_id', 'id');
    }

    public function loan_number()
    {
        return $this->belongsTo(Loan::class, 'loan_id', 'id');
    }

    // public function getCreatedAtAttribute($value) {
//     return date('d,F Y H:m:i', strtotime($value));
// }


    protected static function booted(): void
    {

        static::addGlobalScope('org', function (Builder $query) {



            $query->where('organization_id', auth()->user()->organization_id)
                ->where('branch_id', auth()->user()->branch_id)
                ->orWhere('organization_id', "=", NULL);

        });
    }

}
