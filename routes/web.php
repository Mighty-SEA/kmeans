<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PenerimaController;
use App\Http\Controllers\StatisticController;


Route::get('/', [PenerimaController::class, 'dashboard']);
Route::resource('penerima', App\Http\Controllers\PenerimaController::class);
Route::get('/statistic', [StatisticController::class, 'index'])->name('statistic.index');
Route::get('/statistic/cluster/{cluster}', [App\Http\Controllers\StatisticController::class, 'showCluster'])->name('statistic.cluster');
Route::post('/statistic/recalculate', [StatisticController::class, 'recalculate'])->name('statistic.recalculate');
Route::post('penerima-export', [App\Http\Controllers\PenerimaController::class, 'exportExcel'])->name('penerima.export');
Route::post('penerima-import', [App\Http\Controllers\PenerimaController::class, 'importExcel'])->name('penerima.import');
Route::delete('penerima-bulk-delete', [App\Http\Controllers\PenerimaController::class, 'bulkDelete'])->name('penerima.bulkDelete');
