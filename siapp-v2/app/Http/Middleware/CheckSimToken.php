<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;

class CheckSimToken
{
    public function handle(Request $request, Closure $next)
    {
        $key = $request->header('X-Api-Key') ?? $request->input('api_key');

        if (!$key) {
            return response()->json([
                'status'  => 'error',
                'message' => 'API key tidak ditemukan',
            ], 401);
        }

        $token = ApiKey::findSimToken($key);

        if (!$token || !$token->isValid()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'API key tidak valid atau sudah expired',
            ], 403);
        }

        return $next($request);
    }
}
