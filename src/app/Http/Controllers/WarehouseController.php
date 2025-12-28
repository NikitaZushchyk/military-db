<?php

namespace App\Http\Controllers;

use App\Http\Requests\WarehouseStoreRequest;
use App\Http\Requests\WarehouseUpdateRequest;
use App\Http\Resources\WarehouseResource;
use App\Models\Warehouse;
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

    public function store(WarehouseStoreRequest $request)
    {
        $warehouse = $this->warehouseService->store($request->validated());
        return new WarehouseResource($warehouse);
    }

    public function update(WarehouseUpdateRequest $request,  Warehouse $warehouse)
    {
        $updated_warehouse = $this->warehouseService->update($request->validated(), $warehouse);
        return new WarehouseResource($updated_warehouse);
    }
}
