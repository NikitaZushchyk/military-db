<?php

namespace App\Services;

use App\Models\DutyRoster;
use App\Models\Soldier;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    public function stats()
    {
        return Cache::remember('stats', 600, function () {
            return [
                'soldier_count' => Soldier::count(),
                'free_weapons_count' => Warehouse::where('status', 'in_stock')->count(),
                'issued_weapons_count' => Warehouse::where('status', 'issued')->count(),
                'in_duty_count' => DutyRoster::where('start_time', '<=', now())->where('end_time', '>=', now())->count(),
            ];
        });
    }
}
