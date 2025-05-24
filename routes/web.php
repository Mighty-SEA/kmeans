<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PenerimaController;
use App\Http\Controllers\StatisticController;


Route::get('/', [PenerimaController::class, 'index']);
Route::resource('penerima', App\Http\Controllers\PenerimaController::class);
Route::get('/statistic', [StatisticController::class, 'index'])->name('statistic.index');
Route::get('/statistic/cluster/{cluster}', [App\Http\Controllers\StatisticController::class, 'showCluster'])->name('statistic.cluster');
