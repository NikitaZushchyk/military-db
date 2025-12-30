<?php

namespace App\Http\Controllers;

use App\Http\Resources\DashboardResource;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $service)
    {
    }

    public function stats()
    {
        $stats = $this->service->stats();
        return new DashboardResource($stats);
    }
}
