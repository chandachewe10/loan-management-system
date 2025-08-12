<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;

class Borrower extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use Notifiable;
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
        'first_name',
        'last_name',
        'gender',
        'dob',
        'occupation',
        'identification',
        'mobile',
        'email',
        'address',
        'city',
        'province',
        'zipcode',
        'added_by'

    ];




    public function loan()
    {

        return $this->hasMany(Loan::class, 'id','borrower_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'added_by','id');
    }

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
