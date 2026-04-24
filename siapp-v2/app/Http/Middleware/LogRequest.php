<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogRequest
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Ambil data response untuk dicatat
        $responseBody = $response->getContent();
        $decoded      = json_decode($responseBody, true);
        $pesan        = $decoded['respon'][0]['message'] ?? ($decoded['status'] ?? '-');

        DB::table('tempreq')->insert([
            'ip'     => $request->input('ipa') ?? $request->ip(),
            'req'    => json_encode($request->all()),
            'info'   => $pesan,
            'detail' => $responseBody,
        ]);

        return $response;
    }
}
