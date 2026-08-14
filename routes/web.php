<?php

use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DisplayController;
use App\Http\Controllers\Patient\DashboardController as PatientDashboardController;
use App\Http\Controllers\Patient\QueueController as PatientQueueController;
use App\Http\Controllers\Staff\QueueController as StaffQueueController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| MediQueue Web Routes
|--------------------------------------------------------------------------
|
| Route Groups:
| 1. Public   — Landing page & Public Hospital Waiting Room Screen
| 2. Auth     — Login / Register / Logout (Throttle protected)
| 3. Patient  — Queue joining, status monitoring, history
| 4. Staff    — Queue operations (call, serve, complete, skip, recall)
| 5. Admin    — Services, users, password reset, settings, audit log
|
*/

// ============================================================
// 1. Public Routes
// ============================================================

Route::get('/', function () {
    return view('landing');
})->name('home');

// Hospital / Clinic Public Waiting Room Display TV Screen
Route::get('/display',      [DisplayController::class, 'index'])->name('display');
Route::get('/display/data', [DisplayController::class, 'data'])->name('display.data');

// ============================================================
// 2. Authentication Routes
// ============================================================

Route::middleware('guest')->group(function () {
    Route::get('/login',    [LoginController::class, 'create'])->name('login');
    Route::post('/login',   [LoginController::class, 'store'])->middleware('throttle:10,1');
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register',[RegisterController::class, 'store'])->middleware('throttle:10,1');
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// ============================================================
// 3. Patient Routes
// ============================================================

Route::prefix('patient')
    ->middleware(['auth', 'role:patient'])
    ->name('patient.')
    ->group(function () {
        // Dashboard & Notifications
        Route::get('/dashboard', [PatientDashboardController::class, 'index'])->name('dashboard');
        Route::get('/history',   [PatientDashboardController::class, 'history'])->name('history');
        Route::post('/notifications/read', [PatientDashboardController::class, 'markNotificationsRead'])->name('notifications.read');

        // Queue operations (Rate limited to prevent queue flooding)
        Route::get('/queue',                           [PatientQueueController::class, 'index'])->name('queue.index');
        Route::get('/queue/service/{service}',         [PatientQueueController::class, 'show'])->name('queue.show');
        Route::post('/queue',                          [PatientQueueController::class, 'store'])->middleware('throttle:30,1')->name('queue.store');
        Route::get('/queue/{queueEntry}/status',       [PatientQueueController::class, 'status'])->name('queue.status');
        Route::post('/queue/{queueEntry}/cancel',      [PatientQueueController::class, 'cancel'])->name('queue.cancel');

        // JSON polling endpoint for live updates
        Route::get('/queue/{queueEntry}/status.json',  [PatientQueueController::class, 'statusJson'])->name('queue.status.json');
    });

// ============================================================
// 4. Staff Routes
// ============================================================

Route::prefix('staff')
    ->middleware(['auth', 'role:staff,admin'])
    ->name('staff.')
    ->group(function () {
        Route::get('/dashboard',                       [StaffQueueController::class, 'dashboard'])->name('dashboard');
        Route::post('/queue/call-next',                [StaffQueueController::class, 'callNext'])->name('queue.call-next');
        Route::post('/queue/{queueEntry}/start',       [StaffQueueController::class, 'startService'])->name('queue.start');
        Route::post('/queue/{queueEntry}/complete',    [StaffQueueController::class, 'complete'])->name('queue.complete');
        Route::post('/queue/{queueEntry}/skip',        [StaffQueueController::class, 'skip'])->name('queue.skip');
        Route::post('/queue/{queueEntry}/recall',      [StaffQueueController::class, 'recall'])->name('queue.recall');

        // JSON live status polling for staff dashboard
        Route::get('/queue/live-status',               [StaffQueueController::class, 'liveStatus'])->name('queue.live-status');
    });

// ============================================================
// 5. Admin Routes
// ============================================================

Route::prefix('admin')
    ->middleware(['auth', 'role:admin'])
    ->name('admin.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Service catalogue management
        Route::resource('services', ServiceController::class)->only(['index', 'create', 'store', 'edit', 'update']);
        Route::post('/services/{service}/toggle', [ServiceController::class, 'toggle'])->name('services.toggle');

        // User management & administrative password resets
        Route::get('/users',                              [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create',                       [UserController::class, 'create'])->name('users.create');
        Route::post('/users',                             [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit',                  [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}',                       [UserController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/toggle',               [UserController::class, 'toggle'])->name('users.toggle');
        Route::post('/users/{user}/role',                 [UserController::class, 'updateRole'])->name('users.role');
        Route::post('/users/{user}/reset-password',       [UserController::class, 'resetPassword'])->name('users.reset-password');

        // Clinic & System Settings
        Route::get('/settings',  [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings',  [SettingController::class, 'update'])->name('settings.update');

        // Audit log
        Route::get('/audit', [AuditController::class, 'index'])->name('audit.index');
    });
