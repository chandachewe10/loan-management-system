<?php

namespace App\Models;

use Spatie\Permission\Models\Role as BaseSpatieRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Role extends BaseSpatieRole
{
    use HasFactory;

    // app/Models/Role.php
    protected static function booted(): void
    {


        // Keep your existing org scope
        static::addGlobalScope('org', function (Builder $query) {
            if (auth()->check()) {
                $query->where('roles.organization_id', auth()->user()->organization_id)
                  //  ->where('roles.branch_id', auth()->user()->branch_id)
                    ->orWhereNull('roles.organization_id');
            }
        });
    }
}
