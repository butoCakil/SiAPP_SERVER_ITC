<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckDbToken
{
    public function handle(Request $request, Closure $next)
    {
        $key = $request->input('key');

        if (!$key) {
            return response()->json(['status' => '404', 'message' => 'Permintaan tidak diterima'], 404);
        }

        $token = DB::table('api')
            ->where('kode_api', $key)
            ->where('status', 'aktif')
            ->first();

        if (!$token) {
            return response()->json(['status' => '404', 'message' => '[ERROR][Key] Token tidak terdaftar.'], 404);
        }

        if (now()->toDateString() > $token->masaberlaku) {
            return response()->json(['status' => '404', 'message' => '[ERROR][key] Token sudah kadaluarsa.'], 404);
        }

        return $next($request);
    }
}
