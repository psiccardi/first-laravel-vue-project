<?php
namespace App\Classes;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ErrorHandler {
    public static function handleApiError($method, $e)
    {
        Log::info($method);
        Log::info($e->getMessage());
        Log::info($e->getTraceAsString());
        return response()->json([
            "error" => __("errors.generic_error")
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
