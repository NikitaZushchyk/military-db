<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignmentIssueRequest;
use App\Http\Requests\AssignmentReturnRequest;
use App\Http\Resources\AssignmentResource;
use App\Services\AssignmentService;

class AssignmentController extends Controller
{
    public function __construct(private AssignmentService $assignmentService)
    {
    }

    public function index()
    {
        $assignments = $this->assignmentService->index();
        return AssignmentResource::collection($assignments);
    }

    public function issue(AssignmentIssueRequest $request)
    {
        $result = $this->assignmentService->issue($request->validated());
        return new AssignmentResource($result);
    }

    public function return(AssignmentReturnRequest $request)
    {
        $result = $this->assignmentService->return($request->validated());
        return new AssignmentResource($result);
    }

    public function active()
    {
        $assignments = $this->assignmentService->active();
        return AssignmentResource::collection($assignments);
    }
}
