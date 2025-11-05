<?php


use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthenticatedSessionController;

Route::group(['prefix' => 'admin'], function () {

    Route::middleware(['guest:admin'])->group(function () {
        Route::get('/login', [AdminAuthenticatedSessionController::class, 'create'])->name('admin.login');
        Route::post('/login', [AdminAuthenticatedSessionController::class, 'store'])->name('admin.login.store');
    });

   // Route::middleware(['auth:admin', 'verified'])->group(function () {
        Route::get('dashboard', function () {
            return Inertia::render('admin/dashboard');
        })->name('admin.dashboard');

         Route::post('/logout', [AdminAuthenticatedSessionController::class, 'destroy'])->name('admin.logout');
    //});



});


