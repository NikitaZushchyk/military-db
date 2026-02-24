<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class AssignmentService
{
    public function __construct(private PdfExportService $pdfExportService) {}

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
            'return_date' => now(),
        ]);

        $item = Warehouse::findOrFail($data['warehouse_id']);
        $item->update(['status' => 'in_stock']);

        return $assignment;
    }

    public function index(Request $request)
    {
        $perPage = $request->has('all') ? 1000 : 15;

        return Assignment::query()->with(['soldier', 'item.type'])->orderBy('id', 'desc')->paginate($perPage);
    }

    public function active(Request $request)
    {
        $perPage = $request->has('all') ? 1000 : 15;

        return Assignment::query()->with(['soldier', 'item.type'])->whereNull('return_date')->orderBy('id', 'desc')->paginate($perPage);
    }

    /**
     * @throws \Exception
     */
    public function pdfExport(Request $request)
    {
        $request->merge(['all' => true]);

        if ($request->input('tab') === 'active') {
            $data = $this->active($request);
            $title = 'Журнал видач (Активні)';
        } else {
            $data = $this->index($request);
            $title = 'Журнал видач (Вся історія)';
        }

        $assignments = $data->items();

        $rowsData = [];
        foreach ($assignments as $assignment) {
            $rowsData[] = [
                $assignment->soldier ? $assignment->soldier->last_name.' '.$assignment->soldier->first_name : 'Немає даних',
                $assignment->item ? $assignment->item->name.' ('.$assignment->item->serial_number.')' : 'Немає даних',
                $assignment->issue_date,
                $assignment->return_date ?? 'На руках',
            ];
        }

        $headers = [
            'Боєць',
            'Майно / Серійник',
            'Дата видачі',
            'Дата повернення',
        ];

        return $this->pdfExportService->generate($title, $headers, $rowsData);
    }
}
