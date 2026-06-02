<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LocalOnlyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->server('REMOTE_ADDR') ?? $request->ip();
        if (!in_array($ip, ['127.0.0.1', '::1'])) {
            return response()->json(['status' => 'error', 'message' => 'Forbidden'], 403);
        }
        return $next($request);
    }
}
