<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ExpenseCategory extends Model
{
    use HasFactory;
    use LogsActivity;
    protected $fillable = [
        'category_name',
        'category_code'
    ];

     public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logAll();
    }
   


    public function expense()
    {
        
        return $this->hasMany(Expense::class, 'id','category_id');
    }

}
