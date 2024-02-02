<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BackofficeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware('redirectIfLogged')->get('/login', function () {
    return view('index');
})->name('login');

Route::middleware('locale')->group(function () {
    Route::get("/", [HomeController::class, "getPage"]);
    Route::post("/login", [AuthController::class, "loginWeb"]);
});


Route::middleware(['locale','auth:sanctum', 'adminCheck'])->group(function () {
    Route::get('/backoffice/users', [BackofficeController::class, "getPage"])->name('backoffice.admin.users');
    Route::get('/backoffice/admin/posts', [BackofficeController::class, "getPage"])->name('backoffice.admin.posts');
});

Route::middleware(['locale', 'auth:sanctum'])->group(function () {
    Route::get('/backoffice/profile', [BackofficeController::class, "getPage"])->name('backoffice.all.profile');
    Route::any('/backoffice/{all}', [BackofficeController::class, "getPage"])->where('all', '^(?!api).*$')->name('backoffice');
    Route::get("/logout", [AuthController::class, "logoutWeb"]);
});

Route::get("/{any}", [HomeController::class, "getPage"])->where("any", ".*");
