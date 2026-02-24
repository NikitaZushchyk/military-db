<?php

namespace App\Services;

use App\Models\DutyRoster;
use App\Models\DutyType;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DutyRosterService
{
    public function __construct(private PdfExportService $pdfExportService) {}

    public function index(Request $request)
    {
        $perPage = $request->has('all') ? 1000 : 20;
        $query = DutyRoster::query()->with(['soldier', 'dutyType']);
        $query->when($request->date_from, function ($q) use ($request) {
            $q->where('start_time', '>=', $request->date_from);
        });

        $query->when($request->date_to, function ($q) use ($request) {
            $q->where('start_time', '<=', $request->date_to);
        });
        $duties = $query->orderBy('start_time', 'desc')->paginate($perPage);

        $types = DutyType::select('id', 'name')->get();

        return ['data' => $duties, 'meta_data' => ['types' => $types]];
    }

    public function store(array $data)
    {
        return DutyRoster::create($data)->load('soldier', 'dutyType');
    }

    /**
     * @throws \Exception
     */
    public function pdfExport(Request $request)
    {
        $request->merge(['all' => true]);

        $data = $this->index($request);
        $duties = $data['data']->items();
        $now = now();

        $rowsData = [];
        foreach ($duties as $duty) {
            $start = Carbon::parse($duty->start_time);
            $end = Carbon::parse($duty->end_time);
            if ($now->lt($start)) {
                $status = 'Заплановано';
            } elseif ($now->between($start, $end)) {
                $status = 'В процесі';
            } else {
                $status = 'Завершено';
            }
            $rowsData[] = [
                $duty->soldier ? $duty->soldier->last_name . ' ' . $duty->soldier->first_name : 'Відсутнє',
                $duty->dutyType->name ?? 'Відсутнє',
                $start->format('d.m.Y H:i'),
                $end->format('d.m.Y H:i'),
                $status,
            ];
        }
        $title = 'Графік нарядів';
        $headers = [
            'Боєць',
            'Вид наряду',
            'Початок',
            'Кінець',
            'Статус',
        ];

        return $this->pdfExportService->generate($title, $headers, $rowsData);
    }
}
