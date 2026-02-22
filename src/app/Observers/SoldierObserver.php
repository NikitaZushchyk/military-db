<?php

namespace App\Observers;

use App\Jobs\SendAnalyzeJob;
use App\Models\Soldier;
use App\Services\LoggerClient;
use Illuminate\Support\Facades\Cache;

class SoldierObserver
{
    public function __construct(protected LoggerClient $logger) {}

    public function created(Soldier $soldier): void
    {
        Cache::forget('stats');
        $this->logger->log(
            'SOLDIER_CREATED',
            "New soldier: {$soldier->last_name} {$soldier->first_name} {$soldier->rank->name}"
        );
    }

    public function saved(Soldier $soldier): void
    {
        SendAnalyzeJob::dispatch($soldier->id);
    }

    public function deleted(Soldier $soldier): void
    {
        Cache::forget('stats');
        $this->logger->log(
            'SOLDIER_DELETED',
            "Soldier deleted: {$soldier->last_name} {$soldier->first_name}"
        );
    }
}
