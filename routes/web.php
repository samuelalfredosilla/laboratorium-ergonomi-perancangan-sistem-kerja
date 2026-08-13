<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\Admin\DashboardController;
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
});
