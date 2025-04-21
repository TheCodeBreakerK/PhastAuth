<?php

use App\Api\Controllers\HomeController;
use App\Api\Controllers\UserController;
use App\Api\Http\Route;

require_once __DIR__ . '/../vendor/autoload.php';

Route::get('/', [HomeController::class, 'index']);

Route::group('/users', function () {
    Route::post('/create',   [UserController::class, 'store']);
    Route::post('/login',    [UserController::class, 'login']);
    Route::post('/refresh',  [UserController::class, 'refresh']);
    Route::post('/logout',   [UserController::class, 'logout']);
    Route::get('/fetch',     [UserController::class, 'fetch']);
    Route::put('/update',    [UserController::class, 'update']);
    Route::delete('/delete', [UserController::class, 'delete']);
});
