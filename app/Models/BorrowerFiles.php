<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class BorrowerFiles extends Model
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
        'borrower_id',
        'file_path',
    ];

    public function borrower()
    {
        
        return $this->belongsTo(Borrower::class, 'borrower_id','id');
    }
}
