<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    public function asset_category()
    {

        return $this->belongsTo(AssetCategory::class, 'asset_category_id','id');
    }
}
