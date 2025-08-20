<?php

namespace App\Models;

use Spatie\Activitylog\Models\Activity  as BaseActivityLogs;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ActivityLogs extends BaseActivityLogs
{
    use HasFactory;


    protected static function booted(): void
    {

        static::addGlobalScope('org', function (Builder $query) {

            if (auth()->check()) {

                $query->where('organization_id', auth()->user()->organization_id)
                ->where('branch_id', auth()->user()->branch_id);
            }
        });
    }
}
