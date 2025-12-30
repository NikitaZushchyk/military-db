<?php

namespace App\Http\Controllers;

use App\Http\Requests\SoldierStoreRequest;
use App\Http\Resources\SoldierResource;
use App\Models\Soldier;
use App\Services\SoldierService;
use Illuminate\Http\Request;

class SoldierController extends Controller
{
    public function __construct(private SoldierService $soldierService)
    {
    }

    public function index(Request $request)
    {
        $soldiers = $this->soldierService->index($request);
        return SoldierResource::collection($soldiers['paginator'])->additional(['meta_data' => $soldiers['filters']]);
    }

    public function store(SoldierStoreRequest $request)
    {
        $soldier = $this->soldierService->store($request->validated());
        return new SoldierResource($soldier);
    }

    public function show(Soldier $soldier)
    {
        $result = $this->soldierService->show($soldier);
        return new SoldierResource($result);
    }

    public function update(SoldierStoreRequest $request, Soldier $soldier)
    {
        $result = $this->soldierService->update($request->validated(), $soldier);
        return new SoldierResource($result);
    }

    public function delete(Soldier $soldier)
    {
        $this->soldierService->delete($soldier);
        return response()->json(['message' => 'Солдат видалений']);
    }
}
