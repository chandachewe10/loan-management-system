<?php

namespace App\Observers;

use App\Models\AssetCategory;

class AssetCategoryObserver
{
    /**
     * Handle the AssetCategory "created" event.
     */
    public function created(AssetCategory $assetCategory): void
    {
        $assetCategory->organization_id = auth()->user()->organization_id;
        $assetCategory->branch_id = auth()->user()->branch_id;
        $assetCategory->save();
    }

    /**
     * Handle the AssetCategory "updated" event.
     */
    public function updated(AssetCategory $assetCategory): void
    {
        //
    }

    /**
     * Handle the AssetCategory "deleted" event.
     */
    public function deleted(AssetCategory $assetCategory): void
    {
        //
    }

    /**
     * Handle the AssetCategory "restored" event.
     */
    public function restored(AssetCategory $assetCategory): void
    {
        //
    }

    /**
     * Handle the AssetCategory "force deleted" event.
     */
    public function forceDeleted(AssetCategory $assetCategory): void
    {
        //
    }
}
