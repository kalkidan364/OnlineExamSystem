<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Authentication\Controllers\V1\AuthController;

Route::get('/ping', function () {
    return response()->json(['message' => 'Auth Module is loaded']);
});

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});
