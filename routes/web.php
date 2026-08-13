<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LecturerController;
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
});
