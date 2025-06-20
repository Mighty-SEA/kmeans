<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PenerimaController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;

// Authentication Routes
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/', [PenerimaController::class, 'dashboard']);
    Route::resource('penerima', App\Http\Controllers\PenerimaController::class);
    Route::get('/statistic', [StatisticController::class, 'index'])->name('statistic.index');
    Route::get('/statistic/cluster/{cluster}', [App\Http\Controllers\StatisticController::class, 'showCluster'])->name('statistic.cluster');
    Route::post('/statistic/recalculate', [StatisticController::class, 'recalculate'])->name('statistic.recalculate');
    Route::post('penerima-export', [App\Http\Controllers\PenerimaController::class, 'exportExcel'])->name('penerima.export');
    Route::post('penerima-import', [App\Http\Controllers\PenerimaController::class, 'importExcel'])->name('penerima.import');
    Route::delete('penerima-bulk-delete', [App\Http\Controllers\PenerimaController::class, 'bulkDelete'])->name('penerima.bulkDelete');
    
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
});
