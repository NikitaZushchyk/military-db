<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\Warehouse;

class AssignmentService
{
    public function issue(array $data): Assignment
    {
        $item = Warehouse::findOrFail($data['warehouse_id']);

        $item->update(['status' => 'issued']);

        return Assignment::create($data);
    }

    public function return($data)
    {
        $assignment = Assignment::where('warehouse_id', $data['warehouse_id'])
            ->whereNull('return_date')
            ->firstOrFail();

        $assignment->update([
            'return_date' => now()
        ]);

        $item = Warehouse::findOrFail($data['warehouse_id']);
        $item->update(['status' => 'in_stock']);

        return $assignment;
    }

    public function index()
    {
        $assignments = Assignment::query()->with(['soldier', 'item.type'])->orderBy('id', 'desc')->paginate(15);
        return $assignments;
    }

    public function active()
    {
        $assignments = Assignment::query()->with(['soldier', 'item.type'])->whereNull('return_date')->orderBy('id', 'desc')->paginate(15);
        return $assignments;
    }
}
