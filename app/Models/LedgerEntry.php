<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class LedgerEntry extends Model
{
    use HasFactory;
    protected $fillable = [
         'transaction_id',
         'account_id',
         'debit',
         'credit'
    ];


         public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logAll();
    }


    public function wallet_type()
    {

        return $this->belongsTo(Wallet::class, 'wallet_id','id');
    }


     public function transaction_type()
    {

        return $this->belongsTo(Transaction::class, 'transaction_id','id');
    }

    

    
}
