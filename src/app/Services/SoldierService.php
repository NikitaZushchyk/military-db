<?php

namespace App\Services;

use App\Models\Rank;
use App\Models\Soldier;
use App\Models\Unit;
use Illuminate\Http\Request;

class SoldierService
{
    public function index(Request $request)
    {
        $query = Soldier::query()->with(['rank', 'unit']);
        $query->when($request->unit_id, function ($q) use ($request) {
            $q->where('unit_id', $request->unit_id);
        });

        $query->when($request->rank_id, function ($q) use ($request) {
            $q->where('rank_id', $request->rank_id);
        });

        $query->when($request->status, function ($q) use ($request) {
            $q->where('status', $request->status);
        });
        $query->when($request->search, function ($q) use ($request) {
            $q->where(function ($subQuery) use ($request) {
                $subQuery->where('first_name', 'like', "%{$request->search}%")
                    ->orWhere('last_name', 'like', "%{$request->search}%");
            });
        });

        $soldiers = $query->orderBy('last_name')->paginate(15);

        $ranks = Rank::select('id', 'name')->get();
        $units = Unit::select('id', 'name')->get();

        return [
            'paginator' => $soldiers,
            'filters' => [
                'ranks' => $ranks,
                'units' => $units,
            ],
        ];
    }

    public function store($data)
    {
        return Soldier::create($data);
    }

    public function show(Soldier $soldier)
    {
        return $soldier->load(['rank', 'unit', 'assignments.item', 'duties.dutyType']);
    }

    public function update(array $data, Soldier $soldier)
    {
        $soldier->update($data);

        return $soldier;
    }

    public function delete($soldier)
    {
        return $soldier->delete();
    }
}
