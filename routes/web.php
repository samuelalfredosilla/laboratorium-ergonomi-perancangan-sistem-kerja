<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HomeSliderController;
use App\Http\Controllers\Admin\LecturerController;
use App\Http\Controllers\Admin\NewsController;
use Illuminate\Support\Facades\Route;

// HOME PAGE ROUTE
Route::get('/', [HomeController::class, 'index'])->name('home');

// ABOUT PAGE ROUTES
Route::get('/about/overview', [AboutController::class, 'overview'])->name('about.overview');
Route::get('/about/lecturer', [AboutController::class, 'lecturers'])->name('about.lecturer');
Route::get('/about/lecturer/{id}', [AboutController::class, 'lecturerDetail'])->name('about.lecturer-detail');

// ADMIN PANEL ROUTES
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('lecturers')->name('lecturers.')->group(function () {
        Route::get('/', [LecturerController::class, 'index'])->name('index');
        Route::post('/', [LecturerController::class, 'store'])->name('store');
        Route::get('/{lecturer}', [LecturerController::class, 'show'])->name('show');
        Route::put('/{lecturer}', [LecturerController::class, 'update'])->name('update');
        Route::delete('/{lecturer}', [LecturerController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('news')->name('news.')->group(function () {
        Route::get('/', [NewsController::class, 'index'])->name('index');
        Route::get('/create', [NewsController::class, 'create'])->name('create');
        Route::post('/', [NewsController::class, 'store'])->name('store');
        Route::get('/{news}/edit', [NewsController::class, 'edit'])->name('edit');
        Route::put('/{news}', [NewsController::class, 'update'])->name('update');
        Route::delete('/{news}', [NewsController::class, 'destroy'])->name('destroy');
        Route::patch('/{news}/toggle', [NewsController::class, 'toggle'])->name('toggle');
    });

    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('sliders')->name('sliders.')->group(function () {
        Route::get('/', [HomeSliderController::class, 'index'])->name('index');
        Route::post('/', [HomeSliderController::class, 'store'])->name('store');
        Route::post('/reorder', [HomeSliderController::class, 'reorder'])->name('reorder');
        Route::put('/{slider}', [HomeSliderController::class, 'update'])->name('update');
        Route::delete('/{slider}', [HomeSliderController::class, 'destroy'])->name('destroy');
        Route::patch('/{slider}/toggle', [HomeSliderController::class, 'toggle'])->name('toggle');
    });
});
