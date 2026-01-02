<?php

namespace App\Observers;

use App\Models\Soldier;
use Illuminate\Support\Facades\Cache;

class SoldierObserver
{
    public function created(Soldier $soldier): void
    {
        Cache::forget('stats');
    }

    public function deleted(Soldier $soldier): void
    {
        Cache::forget('stats');
    }
}
