<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThirdParty extends Model
{
    use HasFactory;

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
