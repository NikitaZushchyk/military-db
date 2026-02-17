<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'service' => 'required|string|max:50',
            'action' => 'required|string|max:100',
            'description' => 'nullable|string',
            'created_at' => 'nullable|date',
        ]);

        $data['created_at'] = $data['created_at'] ?? now();

        $log = Log::create($data);

        return response()->json(['id' => $log->id], 201);
    }

    public function index(Request $request)
    {
        $input = $request->input('search');
        $perPage = $request->input('per_page', 20);

        if (!$input || trim($input) === '') {
            return response()->json(
                Log::orderBy('created_at', 'desc')->paginate($perPage)
            );
        }

        $words = explode(' ', $input);
        $queryParts = [];

        foreach ($words as $word) {
            $word = trim($word);
            if ($word === '') continue;

            $escapedWord = addcslashes($word, '+-=&|><!(){}[]^"\\');

            $queryParts[] = "({$escapedWord}~2 OR *{$escapedWord}*)";
        }

        $searchQuery = implode(' AND ', $queryParts);

        $logs = Log::search($searchQuery, function ($client, $body) use ($searchQuery) {
            $params = [
                'index' => 'logs',
                'body'  => [
                    'query' => [
                        'query_string' => [
                            'query' => $searchQuery,
                            'fields' => ['description^3', 'action^2', 'service'],
                            'analyze_wildcard' => true,
                            'default_operator' => 'AND'
                        ],
                    ],
                    'sort' => [
                        ['created_at' => ['order' => 'desc']],
                    ],
                ],
            ];

            return $client->search($params);

        })->paginate($perPage);

        return response()->json($logs);
    }
}
