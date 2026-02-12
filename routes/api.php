<?php

use App\Http\Controllers\Api\WaterReadingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GeminiController;
use Illuminate\Http\Request;

Route::post("/chat", [GeminiController::class, "chat"]);


Route::middleware('auth:sanctum')->get('/check-approval', function (Request $request) {
    return response()->json(['approved' => $request->user()->is_approved]);
});

Route::post('/readings', [WaterReadingController::class, 'store'])->name('api.readings.store');
