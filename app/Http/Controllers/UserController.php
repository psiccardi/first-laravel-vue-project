<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Classes\ErrorHandler;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function delete(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'user_id' => 'required|integer'
        ]);

        if ($validateData->fails()) {
            return response()->json([
                "error" => $validateData->errors()->first()
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $user = User::find($request->user_id);
            if (empty($user)) {
                return response()->json([
                    "error" => __("errors.user_not_found")
                ], Response::HTTP_NOT_FOUND);
            }

            $user->delete();
            return response()->json([
                "id" => $request->user_id
            ]);
        } catch (\Exception $e) {
            return ErrorHandler::handleApiError(__METHOD__, $e);
        }
    }

    public function create(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'password' => 'required|string',
            'confirm_password' => 'required|string',
            'email' => 'required|email:filter|unique:users,email,except,id',
            'role_id' => 'nullable|integer',
        ]);

        if ($validateData->fails()) {
            return response()->json([
                "error" => $validateData->errors()->first()
            ], Response::HTTP_BAD_REQUEST);
        }
        try {
            if ($request->password != $request->confirm_password) {
                return response()->json([
                    "error" => __("errors.password_not_match")
                ], Response::HTTP_BAD_REQUEST);
            }

            $now = (new \DateTime);
            $now->setTimezone(new \DateTimeZone("UTC"));

            $user = new User();
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->role_id = $request->role_id;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->email_verified_at = $now;
            $user->save();

            return response()->json($user);
        } catch (\Exception $e) {
            return ErrorHandler::handleApiError(__METHOD__, $e);
        }
    }

    public function edit(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email:filter',
            'user_id' => 'nullable|integer',
            'role_id' => 'nullable|integer'
        ]);

        if ($validateData->fails()) {
            return response()->json([
                "error" => $validateData->errors()->first()
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            //TODO: check user role
            if (!empty($request->user_id)) {
                $user = User::where("id", $request->user_id)->first();
            } else {
                $user = $request->user();
            }

            if (empty($user)) {
                return response()->json([
                    "error" => __("errors.permission_denied")
                ], Response::HTTP_UNAUTHORIZED);
            }

            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            if (!empty($request->role_id)) {
                $user->role_id = $request->role_id;
            }
            $user->loadAllData();
            $user->save();

            return response()->json($user);
        } catch (\Exception $e) {
            return ErrorHandler::handleApiError(__METHOD__, $e);
        }

    }

    public function editPassword(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'old_password' => 'nullable|string',
            'password' => 'required|string',
            'confirm_password' => 'required|string|same:password',
            'user_id' => 'nullable|integer',
        ]);

        if ($validateData->fails()) {
            return response()->json([
                "error" => $validateData->errors()->first()
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            //TODO: check user role
            if (!empty($request->user_id)) {
                $user = User::where("id", $request->user_id)->first();
            } else {
                $validateData = Validator::make($request->all(), [
                    "old_password" => "required"
                ]);
                if ($validateData->fails()) {
                    return response()->json([
                        "error" => $validateData->errors()->first()
                    ], Response::HTTP_BAD_REQUEST);
                }

                $user = $request->user();

                if (!Hash::check($request->old_password, $user->password)) {
                    return response()->json([
                        "error" => __("errors.invalid_credentials")
                    ], Response::HTTP_BAD_REQUEST);
                }
            }

            if (empty($user)) {
                return response()->json([
                    "error" => __("errors.permission_denied")
                ], Response::HTTP_UNAUTHORIZED);
            }

            $user->password = $request->password;
            $user->save();
            $user->loadAllData();
            return response()->json($user);
        } catch (\Exception $e) {
            return ErrorHandler::handleApiError(__METHOD__, $e);
        }

    }
}
