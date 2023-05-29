<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/users', function (Request $request) {
    return $request->user();
});


Route::get('/', function () {
    p("Hello laravel api !");
});


/**
     * Users Routes
     */
Route::group(['prefix' => 'users',                                         'as' => 'users.'], function () {
    Route::get('/',                                                        [App\Http\Controllers\Api\UserController::class, 'index'])->name('index');
    Route::post('create',                                                  [App\Http\Controllers\Api\UserController::class, 'store'])->name('store');
    Route::get('show/{id}',                                                [App\Http\Controllers\Api\UserController::class, 'show'])->name('show');
    Route::put('update/{id}',                                              [App\Http\Controllers\Api\UserController::class, 'update'])->name('update');
    Route::patch('change-password/{id}',                                   [App\Http\Controllers\Api\UserController::class, 'changePassword'])->name('changePassword');
    Route::delete('delete/{id}',                                           [App\Http\Controllers\Api\UserController::class, 'destroy'])->name('destroy');

    // Auth
    Route::post('register',                                                [App\Http\Controllers\Api\UserController::class, 'register'])->name('register');
    Route::post('login',                                                   [App\Http\Controllers\Api\UserController::class, 'login'])->name('login');


});
