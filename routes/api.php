<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test-db', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\MenuSeeder', '--force' => true]);
        return response()->json([
            'menus' => \App\Models\Menu::all(),
            'msg' => 'Seeded MenuSeeder!'
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

Route::middleware('throttle:api')->group(function () {
    // Products
    Route::prefix('products')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\ProductController::class, 'index']);
        Route::get('/featured', [\App\Http\Controllers\Api\ProductController::class, 'featured']);
        Route::get('/{slug}', [\App\Http\Controllers\Api\ProductController::class, 'show']);
    });

    // Categories
    Route::get('/categories', [\App\Http\Controllers\Api\CategoryController::class, 'index']);
    Route::get('/categories/{slug}', [\App\Http\Controllers\Api\CategoryController::class, 'show']);

    // Menus
    Route::get('/menus/{code}', [\App\Http\Controllers\Api\MenuController::class, 'show']);

    // Brands
    Route::get('/brands', [\App\Http\Controllers\Api\MenuController::class, 'brands']);

    // Countries
    Route::get('/countries', [\App\Http\Controllers\Api\MenuController::class, 'countries']);

    // Collections
    Route::get('/collections', [\App\Http\Controllers\Api\MenuController::class, 'collections']);

    // Transactions/Forms
    Route::post('/inquiries', [\App\Http\Controllers\Api\InquiryController::class, 'store']);
    Route::post('/orders', [\App\Http\Controllers\Api\OrderController::class, 'store']);
});
