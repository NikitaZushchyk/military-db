<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignmentIssueRequest;
use App\Http\Requests\AssignmentReturnRequest;
use App\Http\Resources\AssignmentResource;
use App\Services\AssignmentService;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function __construct(private AssignmentService $assignmentService) {}

    public function index(Request $request)
    {
        $assignments = $this->assignmentService->index($request);

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

    public function active(Request $request)
    {
        $assignments = $this->assignmentService->active($request);

        return AssignmentResource::collection($assignments);
    }

    public function pdfExport(Request $request)
    {
        $pdfBytes = $this->assignmentService->pdfExport($request);

        return response($pdfBytes)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="assignment_report.pdf"');
    }
}
