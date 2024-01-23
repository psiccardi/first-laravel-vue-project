<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Classes\ErrorHandler;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class RolesController extends Controller
{
    public function get(Request $request)
    {
        try {
            $roles = Role::get();

            return response()->json($roles);
        } catch (\Exception $e) {
            return ErrorHandler::handleApiError(__METHOD__, $e);
        }
    }
}
