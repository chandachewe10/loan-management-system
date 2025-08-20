<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;


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
        'added_by'

    ];


    public function user()
    {

        return $this->hasMany(User::class, 'id','branch_manager');
    }

}
