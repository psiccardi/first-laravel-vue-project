<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function logoutWeb(Request $request)
    {
        $user = $request->user();
        $tokenId = Str::before(request()->bearerToken(), '|');
        auth()->user()->tokens()->where('id', $tokenId )->delete();
        auth('web')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return response()->json([
            "success" => true
        ]);
    }
    //
    public function login(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'email' => 'required|email:filter',
            'password' => 'required'
        ]);

        if ($validateData->fails()) {
            return response()->json([
                "error" => $validateData->errors()->first()
            ]);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                "error" => __("errors.invalid_credentials")
            ]);
        }

        $token = auth()->user()->createToken(Str::random(8))->plainTextToken;

        return response()->json([
            'token' => $token
        ]);
    }

    public function loginWeb(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'email' => 'required|email:filter',
            'password' => 'required'
        ]);

        if ($validateData->fails()) {
            return response()->json([
                "error" => $validateData->errors()->first()
            ]);
        }

        if (!Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ])) {
            return response()->json([
                "error" => __("errors.invalid_credentials")
            ]);
        }

        if (empty(auth()->user()->email_verified_at)) {
            Auth::logout();
        }
        // $user = User::where('email', $request->email)->first();

        // if (!$user || !Hash::check($request->password, $user->password)) {
        // }

        $token = auth()->user()->createToken(Str::random(8))->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => auth()->user()
        ]);
    }
}
