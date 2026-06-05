<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('admin_id')) {
            session(['intended_url' => $request->url()]);
            return redirect()->route('login');
        }
        return $next($request);
    }
}
