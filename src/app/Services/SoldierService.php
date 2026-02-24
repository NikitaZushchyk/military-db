<?php

namespace App\Services;

use App\Models\Rank;
use App\Models\Soldier;
use App\Models\Unit;
use Illuminate\Http\Request;
use App\Services\PdfExportService;

class SoldierService
{
    public function __construct(private PdfExportService $pdfExportService){}

    public function index(Request $request)
    {
        $perPage = $request->has('all') ? 1000 : 15;
        $input = $request->input('search');
        $searchQuery = '*';

        if ($input && trim($input) !== '') {
            $words = explode(' ', $input);
            $queryParts = [];

            foreach ($words as $word) {
                if (trim($word) === '') {
                    continue;
                }

                $escapedWord = addcslashes($word, '+-=&|><!(){}[]^"~*?:\\/');

                $queryParts[] = "({$escapedWord}~2 OR *{$escapedWord}*)";
            }

            if (! empty($queryParts)) {
                $searchQuery = implode(' AND ', $queryParts);
            }
        }

        $scoutQuery = Soldier::search($searchQuery);

        if ($request->unit_id) {
            $scoutQuery->where('unit_id', $request->unit_id);
        }
        if ($request->rank_id) {
            $scoutQuery->where('rank_id', $request->rank_id);
        }
        if ($request->status) {
            $scoutQuery->where('status', strtolower($request->status));
        }

        $scoutQuery->query(function ($q) use ($input) {
            $q->with(['rank', 'unit']);
            if (! $input) {
                $q->orderBy('last_name');
            }
        });

        $soldiers = $scoutQuery->paginate($perPage);

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

    /**
     * @throws \Exception
     */
    public function pdfExport(Request $request)
    {
        $request->merge(['all' => true]);
        $data = $this->index($request);
        $soldiers = $data['paginator']->items();
        $statusMap = [
            'active'   => 'В строю',
            'hospital' => 'Шпиталь',
            'vacation' => 'Відпустка',
            'fired'    => 'Звільнений'
        ];
        $rowsData = [];
        foreach ($soldiers as $soldier) {
            $rowsData[] = [
                $soldier->last_name . ' ' . $soldier->first_name,
                $soldier->rank->name ?? 'Немає',
                $soldier->unit->name ?? 'Немає',
                $statusMap[$soldier->status] ?? ucfirst($soldier->status),
            ];
        }
        $title = 'Особовий склад';
        $headers = [
            'ПІБ',
            'Звання',
            'Підрозділ',
            'Статус',
        ];
        return $this->pdfExportService->generate($title, $headers, $rowsData);
    }
}
