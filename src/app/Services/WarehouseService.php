<?php

namespace App\Services;

use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseService
{
    public function index(Request $request)
    {
        $query = Warehouse::query()->with(['type']);
        $query->when($request->status, function ($q) use ($request) {
            $q->where('status', $request->status);
        });

        $query->when($request->type_id, function ($q) use ($request) {
            $q->where('type_id', $request->type_id);
        });
        return $query->orderBy('id', 'desc')->paginate(15);
    }

    public function store(array $data): Warehouse
    {
        return Warehouse::create($data)->load('type');
    }

    public function update(array $data, Warehouse $warehouse)
    {
        $warehouse->update($data);
        return $warehouse;
    }
}
