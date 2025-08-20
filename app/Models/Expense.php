<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Builder;

class Expense extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use LogsActivity;
    use HasFactory;

  public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logAll();
    }
        protected $fillable = [
        'expense_name',
        'expense_amount',
        'expense_vendor',
        'expense_attachment',
        'expense_date',

    ];




    public function expense_category()
    {

        return $this->belongsTo(ExpenseCategory::class, 'category_id','id');
    }


    protected static function booted(): void
    {

        static::addGlobalScope('org', function (Builder $query) {

            if (auth()->check()) {

                $query->where('organization_id', auth()->user()->organization_id)
                 ->where('branch_id', auth()->user()->branch_id)
                ->orWhere('organization_id',"=",NULL);
            }
        });
    }

}
