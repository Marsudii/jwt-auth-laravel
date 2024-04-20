<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UsersController;
use App\Http\Middleware\Login;
use App\Http\Middleware\RefreshToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return response()->json([
        'status' => 'OK',
        'messages' => 'Welcome Laravel API'
    ]);
});

Route::prefix('v1')->group(function () {

    Route::post('login', [AuthController::class, 'login']);

    Route::middleware([RefreshToken::class])->group(function () {
        Route::post('refresh-token', [AuthController::class, 'refreshToken']);
    });

    Route::middleware([Login::class])->group(function () {
        Route::get('users', [UsersController::class, 'all']);
        Route::get('logout', [AuthController::class, 'logout']);
    });


});



