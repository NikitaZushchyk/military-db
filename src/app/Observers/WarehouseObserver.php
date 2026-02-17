<?php

namespace App\Observers;

use App\Models\Warehouse;
use App\Services\LoggerClient;
use Illuminate\Support\Facades\Cache;

class WarehouseObserver
{
    public function __construct(protected LoggerClient $logger) {}

    /**
     * Handle the Warehouse "created" event.
     */
    public function created(Warehouse $warehouse): void
    {
        if ($warehouse->status != 'broken') {
            Cache::forget('stats');
        }
        $this->logger->log(
            'ITEM_CREATED',
            "New item added: {$warehouse->serial_number}"
        );
    }

    /**
     * Handle the Warehouse "updated" event.
     */
    public function updated(Warehouse $warehouse): void
    {
        if ($warehouse->isDirty('status')) {
            Cache::forget('stats');
        }
        $this->logger->log(
            'ITEM_STATUS_CHANGED',
            "Item {$warehouse->serial_number} status changed: {$warehouse->getOriginal('status')} -> {$warehouse->status}"
        );
    }

    /**
     * Handle the Warehouse "deleted" event.
     */
    public function deleted(Warehouse $warehouse): void
    {
        if ($warehouse->status != 'broken') {
            Cache::forget('stats');
        }
        $this->logger->log(
            'ITEM_DELETED',
            "Item removed: {$warehouse->serial_number}"
        );
    }
}
