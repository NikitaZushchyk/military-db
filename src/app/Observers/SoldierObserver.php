<?php

namespace App\Observers;

use App\Models\Log;
use App\Models\Soldier;
use Illuminate\Support\Facades\Cache;

class SoldierObserver
{
    public function created(Soldier $soldier): void
    {
        Cache::forget('stats');
        Log::create([
            'action' => 'SOLDIER_CREATED',
            'description' => "New soldier: {$soldier->last_name} {$soldier->first_name} ({$soldier->rank->name})",
            'created_at' => now(),
        ]);
    }

    public function deleted(Soldier $soldier): void
    {
        Cache::forget('stats');
        Log::create([
            'action' => 'SOLDIER_DELETED',
            'description' => "Soldier deleted: {$soldier->last_name} {$soldier->first_name}",
            'created_at' => now(),
        ]);
    }
}
