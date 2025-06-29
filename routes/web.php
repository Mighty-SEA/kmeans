<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Beneficiary\BeneficiaryController;
use App\Http\Controllers\Statistic\StatisticController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Decision\DecisionController;
use App\Http\Controllers\Documentation\DocumentationController;

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
    Route::get('/', [BeneficiaryController::class, 'dashboard']);
    Route::resource('beneficiary', BeneficiaryController::class);
    Route::get('/statistic', [StatisticController::class, 'index'])->name('statistic.index');
    Route::get('/statistic/cluster/{cluster}', [StatisticController::class, 'showCluster'])->name('statistic.cluster');
    Route::post('/statistic/recalculate', [StatisticController::class, 'recalculate'])->name('statistic.recalculate');
    Route::post('/statistic/clustering', [StatisticController::class, 'doClustering'])->name('statistic.clustering');
    Route::post('beneficiary-export', [BeneficiaryController::class, 'exportExcel'])->name('beneficiary.export');
    Route::post('beneficiary-import', [BeneficiaryController::class, 'importExcel'])->name('beneficiary.import');
    Route::delete('beneficiary-bulk-delete', [BeneficiaryController::class, 'bulkDelete'])->name('beneficiary.bulkDelete');
    
    // Decision Panel Routes
    Route::get('/decision', [DecisionController::class, 'index'])->name('decision.index');
    Route::get('/decision/create', [DecisionController::class, 'create'])->name('decision.create');
    Route::post('/decision', [DecisionController::class, 'store'])->name('decision.store');
    Route::get('/decision/{id}', [DecisionController::class, 'show'])->name('decision.show');
    Route::delete('/decision/{id}', [DecisionController::class, 'destroy'])->name('decision.destroy');
    
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
});

// Route untuk halaman dokumentasi (terisolasi)
Route::get('/documentation', [DocumentationController::class, 'index'])->name('documentation.index');
Route::get('/documentation/model', [DocumentationController::class, 'model'])->name('documentation.model');
Route::get('/documentation/view', [DocumentationController::class, 'view'])->name('documentation.view');
Route::get('/documentation/controller', [DocumentationController::class, 'controller'])->name('documentation.controller');
Route::get('/documentation/route', [DocumentationController::class, 'route'])->name('documentation.route');
Route::get('/documentation/middleware', [DocumentationController::class, 'middleware'])->name('documentation.middleware');
Route::get('/documentation/migration', [DocumentationController::class, 'migration'])->name('documentation.migration');
