<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_name',
        'asset_code',
        'asset_category_id',
        'purchase_date',
        'purchase_cost',
        'supplier',
        'useful_life_years',
        'depreciation_method',
        'depreciation_rate',
        'accumulated_depreciation',
        'net_book_value',
        'location',
        'custodian',
        'status',
        'disposal_date',
        'disposal_value',
        'organization_id',
        'branch_id',
    ];

    public function asset_category()
    {

        return $this->belongsTo(AssetCategory::class, 'asset_category_id','id');
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
