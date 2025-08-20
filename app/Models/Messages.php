<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Builder;

class Messages extends Model
{
    use HasFactory;
    use LogsActivity;

     public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logAll();
    }



    protected static function booted(): void
    {
        static::addGlobalScope('org', function (Builder $query) {
            if (auth()->hasUser()) {
                $query->where('organization_id', auth()->user()->organization_id)
                 ->where('branch_id', auth()->user()->branch_id)
                ->orWhere('organization_id',"=",NULL);

            }
        });
    }
}
