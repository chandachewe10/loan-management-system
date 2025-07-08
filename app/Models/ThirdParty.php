<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ThirdParty extends Model
{
    use HasFactory;
    use LogsActivity;

     public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logAll();
    }
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getIsActiveAttribute($value) {
        if($value){
            return 'Active';
        }
        else{
            return 'In-Active';
        }
        
    }
}
