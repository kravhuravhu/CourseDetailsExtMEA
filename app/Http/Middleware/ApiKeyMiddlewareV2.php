<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiKeyMiddlewareV2
{
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('X-API-Key');
        
        if (!$apiKey || $apiKey !== env('API_KEY_V2', 'cdmea_b5b76664f2cb972124cdc4bb45e3c092')) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or missing API Key'
            ], 401);
        }
        
        return $next($request);
    }
}