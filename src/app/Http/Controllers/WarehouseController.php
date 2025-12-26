<?php

namespace App\Http\Controllers;

use App\Http\Resources\WarehouseResource;
use App\Services\WarehouseService;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function __construct(private WarehouseService $warehouseService)
    {
    }

    public function index(Request $request)
    {
        $warehouses = $this->warehouseService->index($request);
        return WarehouseResource::collection($warehouses);
    }

    public function store()
    {
    }

    public function update()
    {
    }
}
