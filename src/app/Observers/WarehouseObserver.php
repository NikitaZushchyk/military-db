<?php

namespace App\Observers;

use App\Models\Warehouse;
use Illuminate\Support\Facades\Cache;

class WarehouseObserver
{
    /**
     * Handle the Warehouse "created" event.
     */
    public function created(Warehouse $warehouse): void
    {
        if($warehouse->status != 'broken'){
            Cache::forget('stats');
        }
    }

    /**
     * Handle the Warehouse "updated" event.
     */
    public function updated(Warehouse $warehouse): void
    {
        if($warehouse->isDirty('status')){
            Cache::forget('stats');
        }
    }

    /**
     * Handle the Warehouse "deleted" event.
     */
    public function deleted(Warehouse $warehouse): void
    {
        if($warehouse->status != 'broken'){
            Cache::forget('stats');
        }
    }
}
