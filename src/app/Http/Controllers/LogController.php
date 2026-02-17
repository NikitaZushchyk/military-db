<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LogController extends Controller
{
    /**
     * @throws ConnectionException
     */
    public function logs(Request $request)
    {
        $params = $request->all();

        try {
            $response = Http::timeout(3)->get(config('services.logger.url').'/logs', $params);

            if ($response->failed()) {
                return response()->json(['error' => 'Logger service unavailable'], 503);
            }

            return response()->json($response->json());

        } catch (\Exception $e) {
            return response()->json(['error' => 'Connection refused'], 503);
        }
    }
}
