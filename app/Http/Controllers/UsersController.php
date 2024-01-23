<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Classes\ErrorHandler;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    //
    public function get(Request $request) {
        $validateData = Validator::make($request->all(), [
            "page" => "required|integer",
            "limit" => "required|integer|min:1",
            "email" => "nullable|string",
            "full_name" => "nullable|string"
        ]);

        if ($validateData->fails()) {
            return response()->json([
                "error" => $validateData->errors()->first()
            ], Response::HTTP_BAD_REQUEST);
        }
        try {
            $email = $request->email;
            $full_name = $request->full_name;
            $users = User::with('role')->where(function ($query) use ($email, $full_name) {
                    if (!empty($email)) {
                        $query->where('email', 'like', "%$email%");
                    }

                    if (!empty($full_name)) {
                        $query->where(DB::raw("CONCAT(first_name,' ',last_name)"), "like", "%$full_name%");
                    }
                })
                ->skip($request->page * $request->limit)
                ->take($request->limit)
                ->get();

            return response()->json($users);
        } catch (\Exception $e) {
            return ErrorHandler::handleApiError(__METHOD__, $e);
        }
    }
}
