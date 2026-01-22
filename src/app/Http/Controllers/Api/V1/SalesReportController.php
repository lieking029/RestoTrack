<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SalesReportController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return response()->json([
            'daily'   => [],
            'weekly'  => [],
            'monthly' => [],
        ]);
    }
}
