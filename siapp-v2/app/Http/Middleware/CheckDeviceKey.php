<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;

class CheckDeviceKey
{
    public function handle(Request $request, Closure $next)
    {
        $key = $request->input('key') ?? $request->header('X-Device-Key');

        if (!$key) {
            return response()->json([
                'respon' => [['message' => '405', 'info' => 'ERROR!--REQ TIDAK DI IJINKAN']]
            ], 401);
        }

        $token = ApiKey::findDeviceToken($key);

        if (!$token || !$token->isValid()) {
            return response()->json([
                'respon' => [['message' => '405', 'info' => 'ERROR!--REQ TIDAK DI IJINKAN']]
            ], 401);
        }

        return $next($request);
    }
}
