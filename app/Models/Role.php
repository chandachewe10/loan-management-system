<?php

namespace App\Models;
use Spatie\Permission\Models\Role as BaseSpatieRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Role extends BaseSpatieRole
{
    use HasFactory;

    protected static function booted(): void
{
    static::addGlobalScope('org', function (Builder $query) {
        if (auth()->check()) {
            $query->where('roles.organization_id', auth()->user()->organization_id)
            ->orWhere('roles.organization_id',"=",NULL);
        }
    });
}
}
