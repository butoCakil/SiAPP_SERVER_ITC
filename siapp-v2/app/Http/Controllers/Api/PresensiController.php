<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PresensiService;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    public function __construct(private PresensiService $service) {}

    public function tag(Request $request)
    {
        $result = $this->service->prosesTag($request->all());
        return response()->json($result);
    }
}
