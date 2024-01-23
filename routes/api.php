<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UsersController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['customSanctumAuthentication', 'locale', 'adminCheckAPI'])->group(function () {
    Route::get("/users", [UsersController::class, "get"]);
    Route::delete("/user", [UserController::class, "delete"]);
    Route::post("/user", [UserController::class, "create"]);
    Route::get("/admin/posts", [PostsController::class, "getAdmin"]);
});

Route::middleware(['customSanctumAuthentication', 'locale'])->group(function () {
    Route::get('/user', function (Request $request) {
        $user = $request->user();
        $user->loadAllData();
        return $user;
    });


    Route::put("/user", [UserController::class, "edit"]);
    Route::put("/user/password", [UserController::class, "editPassword"]);
    Route::get("/roles", [RolesController::class, "get"]);
});

Route::middleware('locale')->group(function () {
    Route::post("/login", [AuthController::class, "login"]);
});
