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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logAll();
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

            if (auth()->check()) {

                $query->where('organization_id', auth()->user()->organization_id)
                ->orWhere('organization_id',"=",NULL);
            }
        });
    }

}
