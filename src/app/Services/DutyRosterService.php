<?php

namespace App\Services;

use App\Models\DutyRoster;
use Illuminate\Http\Request;

class DutyRosterService
{
    public function index(Request $request)
    {
        $query = DutyRoster::query()->with(['soldier', 'dutyType']);
        $query->when($request->date_from, function ($q) use ($request) {
            $q->where('start_time', '>=', $request->date_from);
        });

        $query->when($request->date_to, function ($q) use ($request) {
            $q->where('start_time', '=<', $request->date_to);
        });
        return $query->orderBy('start_time', 'desc')->paginate(20);
    }

    public function store(array $data)
    {
        return DutyRoster::create($data)->load('soldier', 'dutyType');
    }
}
