<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DatasetController;
use App\Http\Controllers\Admin\ModelController;
use App\Http\Controllers\Admin\SearchHistoryController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Only admin users authenticate in this application. Public visitors never
| log in — see routes/web.php for the public site.
|
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AuthController::class, 'login']);
    });

    Route::middleware('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('kategori', CategoryController::class)
            ->except(['show'])
            ->names('categories')
            ->parameters(['kategori' => 'category']);

        Route::resource('layanan', ServiceController::class)
            ->except(['show'])
            ->names('services')
            ->parameters(['layanan' => 'service']);

        Route::get('riwayat', [SearchHistoryController::class, 'index'])->name('search-histories.index');
        Route::get('riwayat/export', [SearchHistoryController::class, 'export'])->name('search-histories.export');

        Route::get('model-ai', [ModelController::class, 'index'])->name('model.index');
        Route::post('model-ai/retrain', [ModelController::class, 'retrain'])->name('model.retrain');

        Route::get('dataset/export', [DatasetController::class, 'export'])->name('dataset.export');
        Route::resource('dataset', DatasetController::class)
            ->except(['show'])
            ->names('dataset');

        Route::get('pengaturan', [SettingsController::class, 'index'])->name('settings.index');
    });
});
