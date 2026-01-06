<?php

namespace App\Services;

use App\Models\EquipmentType;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseService
{
    public function index(Request $request)
    {
        $perPage = $request->has('all') ? 1000 : 15;
        $input = $request->input('search');

        if ($input && trim($input) !== '') {
            $words = explode(' ', $input);
            $queryParts = [];

            foreach ($words as $word) {
                if (trim($word) === '') continue;

                $escapedWord = addcslashes($word, '+-=&|><!(){}[]^"~*?:\\/');

                $fuzziness = mb_strlen($word) >= 5 ? '~2' : '~1';

                $queryParts[] = "({$escapedWord}{$fuzziness} OR *{$escapedWord}*)";
            }

            $searchQuery = implode(' AND ', $queryParts);
        } else {
            $searchQuery = '*';
        }

        $scoutQuery = Warehouse::search($searchQuery);

        if ($request->status) {
            $scoutQuery->where('status', $request->status);
        }
        if ($request->type_id) {
            $scoutQuery->where('equipment_type_id', $request->type_id);
        }

        $scoutQuery->query(function ($q) use ($input) {
            $q->with(['type']);
            if (!$input) {
                $q->orderBy('id', 'desc');
            }
        });

        $warehouse = $scoutQuery->paginate($perPage);

        $types = EquipmentType::select('id', 'name')->get();
        return [
            'warehouses' => $warehouse,
            'filters' => [
                'types' => $types,
                'statuses' => ['in_stock', 'issued', 'broken'],
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
