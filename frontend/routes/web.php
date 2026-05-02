<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CurrencyDetectionController;
use App\Http\Controllers\ExchangeRateController;

Route::get('/', [CurrencyDetectionController::class, 'index'])->name('detect.index');
Route::post('/predict', [CurrencyDetectionController::class, 'predict'])->name('predict');

Route::get('/api/convert', [ExchangeRateController::class, 'convert'])->name('currency.convert');
Route::get('/api/rates', [ExchangeRateController::class, 'getRates'])->name('currency.rates');

