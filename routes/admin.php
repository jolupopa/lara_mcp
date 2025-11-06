<?php


use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthenticatedSessionController;
use App\Http\Controllers\Admin\Settings\AdminPasswordController;
use App\Http\Controllers\Admin\Settings\AdminProfileController;

Route::group(['prefix' => 'admin'], function () {

    Route::middleware(['guest:admin'])->group(function () {
        Route::get('/login', [AdminAuthenticatedSessionController::class, 'create'])->name('admin.login');
        Route::post('/login', [AdminAuthenticatedSessionController::class, 'store'])->name('admin.login.store');
    });

    Route::middleware(['auth:admin'])->group(function () { // 'verified' puede ser añadido si se implementa verificación de email para admins
        Route::get('dashboard', function () {
            return Inertia::render('admin/dashboard');
        })->name('admin.dashboard');

        Route::post('/logout', [AdminAuthenticatedSessionController::class, 'destroy'])->name('admin.logout');

        // Admin Profile Settings
        Route::get('/profile', [AdminProfileController::class, 'edit'])->name('admin.profile.edit');
        Route::patch('/profile', [AdminProfileController::class, 'update'])->name('admin.profile.update');
        Route::delete('/profile', [AdminProfileController::class, 'destroy'])->name('admin.profile.destroy');

        // Admin Password Settings
        Route::get('/password', [AdminPasswordController::class, 'edit'])->name('admin.password.edit');
        Route::put('/password', [AdminPasswordController::class, 'update'])->name('admin.password.update');

        // Admin Appearance Settings
        Route::get('/appearance', function () {
            return Inertia::render('admin/settings/appearance');
        })->name('admin.appearance.edit');
    });
});
