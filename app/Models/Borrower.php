<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Borrower extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use Notifiable;

    public function getCreatedAtAttribute($value)
    {
        return date('d,F Y H:m:i', strtotime($value));
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['first_name', 'last_name', 'gender', 'dob', 'occupation', 'identification', 'mobile', 'email', 'address', 'city', 'province', 'zipcode', 'added_by'];

    public function files()
    {
        return $this->hasMany(BorrowerFiles::class, 'id', 'borrower_id');
    }

    public function loan()
    {
        return $this->hasMany(Loan::class, 'id', 'borrower_id');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'added_by', 'id');
    }

    public function loans()
    {
        return $this->hasMany(Loan::class, 'borrower_id', 'id');
    }
}
