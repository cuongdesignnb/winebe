<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\RegionController;
use App\Http\Controllers\Admin\DistilleryController;
use App\Http\Controllers\Admin\CollectionController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\CmsPageController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\InquiryController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Catalog
    Route::resource('products', ProductController::class);
    Route::resource('brands', BrandController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('countries', CountryController::class);
    Route::resource('regions', RegionController::class);
    Route::resource('distilleries', DistilleryController::class);
    Route::resource('collections', CollectionController::class);

    // Media Library
    Route::get('media', [MediaController::class, 'index'])->name('media.index');
    Route::post('media/api/folders', [MediaController::class, 'storeFolder'])->name('media.api.folders.store');
    Route::get('media/api/folders', [MediaController::class, 'getFolders'])->name('media.api.folders.index');
    Route::delete('media/api/folders/{id}', [MediaController::class, 'destroyFolder'])->name('media.api.folders.destroy');
    Route::post('media/api/assets', [MediaController::class, 'storeAsset'])->name('media.api.assets.store');
    Route::delete('media/api/assets/{id}', [MediaController::class, 'destroyAsset'])->name('media.api.assets.destroy');

    // Navigation Menus
    Route::resource('menus', MenuController::class);
    Route::post('menus/{menu}/items', [MenuController::class, 'storeItem'])->name('menus.items.store');
    Route::put('menus/{menu}/items/{item}', [MenuController::class, 'updateItem'])->name('menus.items.update');
    Route::delete('menus/{menu}/items/{item}', [MenuController::class, 'destroyItem'])->name('menus.items.destroy');
    Route::post('menus/{menu}/items/reorder', [MenuController::class, 'reorderItems'])->name('menus.items.reorder');

    // CMS Pages
    Route::resource('pages', CmsPageController::class);

    // Orders & Inquiries
    Route::resource('orders', OrderController::class)->only(['index', 'show', 'update']);
    Route::resource('inquiries', InquiryController::class)->only(['index', 'update', 'destroy']);

    // Placeholders
    $placeholders = [
        'promotions' => 'Voucher & Khuyến mãi',
        'analytics' => 'Báo cáo & Phân tích',
        'users' => 'Nhân viên & Thành viên',
        'settings' => 'Cấu hình hệ thống'
    ];

    foreach ($placeholders as $slug => $title) {
        Route::get($slug, function () use ($title) {
            return Inertia::render('Admin/Placeholder', ['title' => $title]);
        })->name("$slug.index");
    }
});

