<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\Admin\AssistantController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HomeSliderController;
use App\Http\Controllers\Admin\LecturerController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\SearchController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController as PublicNewsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\OrganizationStructureController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\Admin\PracticumTaskController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\AchievementController;
use App\Http\Controllers\Admin\AchievementController as AdminAchievementController;
use App\Http\Controllers\ProcedureController;
use App\Http\Controllers\Admin\LabProcedureController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\SitemapController;

/*
|--------------------------------------------------------------------------
| 1. PUBLIC / FRONTEND ROUTES
|--------------------------------------------------------------------------
*/

// sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'index']);

// Home Page
Route::get('/', [HomeController::class, 'index'])->name('home');

// About Pages
Route::prefix('about')->name('about.')->group(function () {
    Route::get('/overview', [AboutController::class, 'overview'])->name('overview');
    Route::get('/lecturer', [AboutController::class, 'lecturers'])->name('lecturer');
    Route::get('/lecturer/{id}', [AboutController::class, 'lecturerDetail'])->name('lecturer-detail');
    Route::get('/epsikers', [AboutController::class, 'epsikers'])->name('epsikers');
    Route::get('/structure', [AboutController::class, 'structure'])->name('structure');
});

// Activities Pages
Route::prefix('activities')->name('activities.')->group(function () {
    Route::get('/practicum', [ActivityController::class, 'practicum'])->name('practicum');
    Route::get('/events', [ActivityController::class, 'events'])->name('events');

    Route::get('/events/{id}', [ActivityController::class, 'eventDetail'])->name('events.detail');
    Route::get('/practicum/{id}', [ActivityController::class, 'practicumDetail'])->name('practicum.detail');
});

// Facilities Pages
Route::prefix('facilities')->name('facilities.')->group(function () {
    Route::get('/equipment', [FacilityController::class, 'equipment'])->name('equipment');
});

// Achievement Pages
Route::get('/achievement', [AchievementController::class, 'index'])->name('achievement.index');
Route::get('/achievement/{id}', [AchievementController::class, 'show'])->name('achievement.show');

// Public News & Articles
Route::get('/news', [PublicNewsController::class, 'index'])->name('news.index');
Route::get('/news/{news:slug}', [PublicNewsController::class, 'show'])->name('news.show');

// Lab Procedures Pages
Route::get('/lab-procedures', [ProcedureController::class, 'index'])->name('procedures.index');

/*
|--------------------------------------------------------------------------
| 2. AUTHENTICATION ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.attempt');

    // Rute Two-Factor Authentication (2FA)
    Route::get('/login/verify-2fa', [LoginController::class, 'show2FaForm'])->name('login.2fa');
    Route::post('/login/verify-2fa', [LoginController::class, 'verify2Fa'])->name('login.2fa.verify');
    Route::post('/login/resend-2fa', [LoginController::class, 'resend2Fa'])->name('login.2fa.resend');
});

Route::post('/logout', [LoginController::class, 'destroy'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| 3. ADMIN PANEL ROUTES (Protected: auth + role:admin)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {

        // Dashboard, Search, & Global Notifications
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [DashboardController::class, 'index']); // Alias agar /admin/dashboard tetap bekerja
        Route::get('/search', [SearchController::class, 'index'])->name('search');
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');

        // 1. Lecturers & Staff
        Route::prefix('lecturers')->name('lecturers.')->group(function () {
            Route::get('/', [LecturerController::class, 'index'])->name('index');
            Route::post('/', [LecturerController::class, 'store'])->name('store');
            Route::get('/{lecturer}', [LecturerController::class, 'show'])->name('show');
            Route::put('/{lecturer}', [LecturerController::class, 'update'])->name('update');
            Route::delete('/{lecturer}', [LecturerController::class, 'destroy'])->name('destroy');
        });

        // 2. EPSIKERS & Kelola Periode Asisten
        Route::prefix('assistants')->name('assistants.')->group(function () {
            Route::post('/periods/store', [AssistantController::class, 'storePeriod'])->name('periods.store');
            Route::put('/periods/rename', [AssistantController::class, 'renamePeriod'])->name('periods.rename');
            Route::delete('/periods/destroy', [AssistantController::class, 'destroyPeriod'])->name('periods.destroy');
            Route::patch('/periods/{period}/toggle', [AssistantController::class, 'togglePeriod'])->name('periods.toggle');
        });
        Route::resource('assistants', AssistantController::class);

        // 3. News & Articles Management
        Route::prefix('news')->name('news.')->group(function () {
            Route::get('/', [NewsController::class, 'index'])->name('index');
            Route::post('/', [NewsController::class, 'store'])->name('store');
            Route::get('/{news}', [NewsController::class, 'show'])->name('show');
            Route::put('/{news}', [NewsController::class, 'update'])->name('update');
            Route::delete('/{news}', [NewsController::class, 'destroy'])->name('destroy');
            Route::patch('/{news}/toggle', [NewsController::class, 'toggle'])->name('toggle');
        });

        // 4. Home Sliders Management
        Route::prefix('sliders')->name('sliders.')->group(function () {
            Route::get('/', [HomeSliderController::class, 'index'])->name('index');
            Route::post('/', [HomeSliderController::class, 'store'])->name('store');
            Route::post('/reorder', [HomeSliderController::class, 'reorder'])->name('reorder');
            Route::put('/{slider}', [HomeSliderController::class, 'update'])->name('update');
            Route::delete('/{slider}', [HomeSliderController::class, 'destroy'])->name('destroy');
            Route::patch('/{slider}/toggle', [HomeSliderController::class, 'toggle'])->name('toggle');
        });

        // 5. Organization Structure Management
        Route::prefix('organization-structure')->name('organization-structure.')->group(function () {
            Route::get('/', [OrganizationStructureController::class, 'index'])->name('index');
            Route::put('/update', [OrganizationStructureController::class, 'update'])->name('update');
            Route::delete('/destroy', [OrganizationStructureController::class, 'destroy'])->name('destroy');
        });

        // 6. Practicum Activities Management
        Route::prefix('practicum')->name('practicum.')->group(function () {
            Route::get('/', [PracticumTaskController::class, 'index'])->name('index');
            Route::post('/', [PracticumTaskController::class, 'store'])->name('store');
            Route::get('/{practicum}', [PracticumTaskController::class, 'show'])->name('show');
            Route::put('/{practicum}', [PracticumTaskController::class, 'update'])->name('update');
            Route::delete('/{practicum}', [PracticumTaskController::class, 'destroy'])->name('destroy');
        });

        // 7. Events Activities Management
        Route::prefix('events')->name('events.')->group(function () {
            Route::get('/', [EventController::class, 'index'])->name('index');
            Route::post('/', [EventController::class, 'store'])->name('store');
            Route::get('/{event}', [EventController::class, 'show'])->name('show');
            Route::put('/{event}', [EventController::class, 'update'])->name('update');
            Route::delete('/{event}', [EventController::class, 'destroy'])->name('destroy');
        });

        // 8. Equipment Management
        Route::prefix('equipment')->name('equipment.')->group(function () {
            // Rute Kategori Peralatan (Wajib diletakkan sebelum rute berparameter {equipment})
            Route::post('/categories/store', [EquipmentController::class, 'storeCategory'])->name('categories.store');
            Route::put('/categories/rename', [EquipmentController::class, 'renameCategory'])->name('categories.rename');
            Route::delete('/categories/destroy', [EquipmentController::class, 'destroyCategory'])->name('categories.destroy');

            // Rute Utama Peralatan
            Route::get('/', [EquipmentController::class, 'index'])->name('index');
            Route::post('/', [EquipmentController::class, 'store'])->name('store');
            Route::get('/{equipment}', [EquipmentController::class, 'show'])->name('show');
            Route::put('/{equipment}', [EquipmentController::class, 'update'])->name('update');
            Route::delete('/{equipment}', [EquipmentController::class, 'destroy'])->name('destroy');
        });

        // 9. Achievement Management (ADMIN)
        Route::prefix('achievements')->name('achievements.')->group(function () {
            Route::get('/', [AdminAchievementController::class, 'index'])->name('index');
            Route::post('/', [AdminAchievementController::class, 'store'])->name('store');
            Route::get('/{achievement}', [AdminAchievementController::class, 'show'])->name('show');
            Route::put('/{achievement}', [AdminAchievementController::class, 'update'])->name('update');
            Route::delete('/{achievement}', [AdminAchievementController::class, 'destroy'])->name('destroy');
            Route::patch('/{achievement}/toggle', [AdminAchievementController::class, 'toggle'])->name('toggle');
        });
        // 10. Lab Procedures (ADMIN)
        Route::prefix('lab-procedures')->name('procedures.')->group(function () {
            Route::get('/', [LabProcedureController::class, 'index'])->name('index');
            Route::post('/', [LabProcedureController::class, 'store'])->name('store');
            Route::put('/{procedure}', [LabProcedureController::class, 'update'])->name('update');
            Route::delete('/{procedure}', [LabProcedureController::class, 'destroy'])->name('destroy');
        });

        // 11. categories
        Route::prefix('categories')->name('categories.')->group(function () {
            Route::post('/', [CategoryController::class, 'store'])->name('store');
            Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
            Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
        });

        // 12. Site-settings
        Route::prefix('site-settings')->name('site-settings.')->group(function () {
            Route::get('/', [SiteSettingController::class, 'index'])->name('index');
            Route::put('/', [SiteSettingController::class, 'update'])->name('update');
        });

        // 13. edit profile
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('edit');
            Route::put('/', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('update');
        });
    });
