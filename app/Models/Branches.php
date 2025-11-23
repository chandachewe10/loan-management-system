<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Builder;


class Branches extends Model
{
    use HasFactory;
    use LogsActivity;

  public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logAll();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'branch_name',
        'street',
        'address',
        'mobile',
        'email',
        'address',
        'city',
        'province',
        'branch_manager',
        'zipcode',
        'added_by',
        'organization_id'

    ];


    public function manager()
    {
        return $this->belongsTo(User::class, 'branch_manager', 'id');
    }
    
    public function user()
    {
        // Keep for backward compatibility
        return $this->belongsTo(User::class, 'branch_manager', 'id');
    }

     protected static function booted(): void
    {

        static::addGlobalScope('org', function (Builder $query) {

            if (auth()->check()) {

                $query->where('organization_id', auth()->user()->organization_id)
                // ->where('branch_id', auth()->user()->branch_id)
                ->orWhere('organization_id',"=",NULL);
            }
        });
    }

}
