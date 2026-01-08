<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/telegram/webhook', [\App\Http\Controllers\TelegramController::class, 'handle']);

Route::prefix('v1')->middleware('set-api-locale')->group(function () {

    Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);
    Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register'])->middleware('check.role');
    Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->middleware('auth:sanctum');

    Route::prefix('categories')->group(function () {
        Route::get('/', [\App\Http\Controllers\CategoryController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\CategoryController::class, 'store']);
        Route::get('/{id}', [\App\Http\Controllers\CategoryController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\CategoryController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\CategoryController::class, 'destroy']);
    });

    Route::prefix('products')->group(function () {
        Route::get('/', [\App\Http\Controllers\ProductController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\ProductController::class, 'store']);
        Route::get('/{id}', [\App\Http\Controllers\ProductController::class, 'show']);
        Route::match(['put', 'patch'], '/{id}', [\App\Http\Controllers\ProductController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\ProductController::class, 'destroy']);
    });

    Route::prefix('clients')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [\App\Http\Controllers\ClientController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\ClientController::class, 'store']);
        Route::get('/{id}', [\App\Http\Controllers\ClientController::class, 'show']);
        Route::put('/{id}', [\App\Http\Controllers\ClientController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\ClientController::class, 'destroy']);
    });
});
