<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return response()->json(['message' => 'API working']);
});

Route::group(['prefix' => 'v1/auth'], function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::group(['prefix' => 'v1/tasks', 'middleware' => ['auth:sanctum', 'api.response']], function () {
    Route::get('/list', [TaskController::class, 'index']);
    Route::post('/create', [TaskController::class, 'create']);
    Route::get('/details/{id}', [TaskController::class, 'show']);
    Route::patch('/update/{id}', [TaskController::class, 'update']);
    Route::delete('/delete/{id}', [TaskController::class, 'destroy']);
    Route::patch('/status/{id}', [TaskController::class, 'markCompleted']);
});