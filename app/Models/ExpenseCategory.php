<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'category_name',
        'category_code'
    ];

    public function getCreatedAtAttribute($value) {
        return date('d,F Y H:m:i', strtotime($value));
    }


    public function expense()
    {
        
        return $this->hasMany(Expense::class, 'id','category_id');
    }

}
