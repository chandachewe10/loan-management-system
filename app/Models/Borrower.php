<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrower extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    

    public function getCreatedAtAttribute($value) {
        return date('d,F Y H:m:i', strtotime($value));
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
        

    ];

    public function files()
    {
        // Primary id in parent table and the Foreign key in child table
        return $this->hasMany(BorrowerFiles::class, 'id','borrower_id');
    }




}
