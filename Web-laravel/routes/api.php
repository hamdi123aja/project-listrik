<?php

use App\Http\Controllers\Api\SensorReadingController;
use Illuminate\Support\Facades\Route;

Route::post('/sensor-readings', [SensorReadingController::class, 'store'])->name('api.sensor-readings.store');
Route::post('/pzem/readings', [SensorReadingController::class, 'store'])->name('api.pzem-readings.store');
Route::get('/sensor-readings/latest', [SensorReadingController::class, 'latest'])->name('api.sensor-readings.latest');
Route::get('/sensor-readings/daily-chart', [SensorReadingController::class, 'dailyChart'])->name('api.sensor-readings.daily-chart');
Route::get('/sensor-readings', [SensorReadingController::class, 'index'])->name('api.sensor-readings.index');
