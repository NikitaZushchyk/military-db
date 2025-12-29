<?php

namespace App\Http\Controllers;

use App\Http\Requests\DutyRosterStoreRequest;
use App\Http\Resources\DutyRosterResource;
use App\Services\DutyRosterService;
use Illuminate\Http\Request;

class DutyRosterController extends Controller
{
    public function __construct(private DutyRosterService $service)
    {
    }

    public function index(Request $request)
    {
        $duties = $this->service->index($request);
        return DutyRosterResource::collection($duties);
    }

    public function store(DutyRosterStoreRequest $request)
    {
        $dutyRoster = $this->service->store($request->validated());
        return new DutyRosterResource($dutyRoster);
    }
}
