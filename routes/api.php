<?php

use App\Http\Controllers\Api\WaterReadingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GeminiController;

Route::post("/chat", [GeminiController::class, "chat"]);


Route::post('/readings', [WaterReadingController::class, 'store'])->name('api.readings.store');
