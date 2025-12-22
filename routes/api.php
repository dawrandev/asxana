<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->middleware('set-api-locale')->group(function () {

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
        Route::put('/{id}', [\App\Http\Controllers\ProductController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\ProductController::class, 'destroy']);
    });
});
