<?php

namespace App\Services;

use App\Models\EquipmentType;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseService
{
    public function index(Request $request)
    {
        $query = Warehouse::query()->with(['type']);

        $query->when($request->status, function ($q, $status) {
            $q->where('status', $status);
        });
        $query->when($request->type_id, function ($q, $id) {
            $q->where('equipment_type_id', $id);
        });

        $warehouse = $query->orderBy('id', 'desc')->paginate(15);
        $types = EquipmentType::select('id', 'name')->get();
        return [
            'warehouses' => $warehouse,
            'filters' => [
                'types' => $types,
                'statuses' => ['in_stock', 'issued', 'maintenance', 'lost'],
            ],
        ];
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
