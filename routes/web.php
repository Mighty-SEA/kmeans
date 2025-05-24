<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PenerimaController;


Route::get('/', [PenerimaController::class, 'index']);
Route::resource('penerima', App\Http\Controllers\PenerimaController::class);
