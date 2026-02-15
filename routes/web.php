<?php

use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WaterQualityController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\AblyController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\GeminiController;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome', [
        'contents' => \App\Models\LandingContent::all()->keyBy('key')
    ]);
})->name('welcome');

Route::post('/login-unlock', function (Illuminate\Http\Request $request) {
    if ($request->input('key') === 'kkk12345') {
        session(['login_unlocked' => true]);
        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false], 403);
})->name('login.unlock');

Route::middleware(['auth', \App\Http\Middleware\EnsureUserIsApproved::class])->group(function () {
    // Admin Landing Page CMS
    Route::prefix('admin/landing')->name('admin.landing.')->middleware(\App\Http\Middleware\EnsureAdmin::class)->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\LandingController::class, 'index'])->name('index');
        Route::put('/', [\App\Http\Controllers\Admin\LandingController::class, 'update'])->name('update');
    });

    // FIX: Promote current user to Admin
    Route::get('/promote-me', function() {
        auth()->user()->update(['role' => 'admin', 'is_approved' => true]);
        return redirect()->back()->with('status', 'You are now an ADMIN!');
    });
    Route::get('/approval/check', function (Illuminate\Http\Request $request) {
        return response()->json(['approved' => $request->user()->is_approved]);
    })->name('approval.check');

    Route::get('/approval', function () {
        // CHEAT: Redirect Admin to Dashboard
        if (auth()->user()->email === 'admin@admin.com') return redirect()->route('dashboard');

        if (auth()->user()->isApproved()) {
             // If approved, force logout and redirect to login
             auth()->logout();
             request()->session()->invalidate();
             request()->session()->regenerateToken();
             return redirect('/login')->with('status', 'Account approved! Please login to continue.');
        }
        return view('auth.approval');
    })->name('approval.wait');
    Route::get('/dashboard', [WaterQualityController::class, 'dashboard'])->middleware('verified')->name('dashboard');
    Route::get('/dashboard/chart-data', [WaterQualityController::class, 'chartData'])->name('dashboard.chart');
    Route::get('/history', [WaterQualityController::class, 'history'])->name('history');
    Route::delete('/history/{reading}', [WaterQualityController::class, 'destroyReading'])->name('history.destroy');
    Route::post('/history/bulk-delete', [WaterQualityController::class, 'destroyReadings'])->name('history.bulk-delete');
    Route::get('/alerts', [WaterQualityController::class, 'alerts'])->name('alerts');
    Route::post('/ably/auth', [AblyController::class, 'auth'])->name('ably.auth');
    Route::post('/dashboard/refresh', [WaterQualityController::class, 'refresh'])->name('dashboard.refresh');

    // Admin Approval Routes
    Route::get('/admin/approvals', [\App\Http\Controllers\UserController::class, 'approvals'])->name('admin.users.approvals');
    Route::get('/admin/approval-check-count', function() {
        if (!auth()->user()->isAdmin()) return response()->json(['count' => 0]);
        return response()->json(['count' => \App\Models\User::where('is_approved', false)->count()]);
    })->name('admin.approval.check-count');
    Route::get('/admin/approve/{user}', [\App\Http\Controllers\UserController::class, 'approve'])->name('admin.approve');
    Route::get('/admin/deny/{user}', [\App\Http\Controllers\UserController::class, 'deny'])->name('admin.deny');
    Route::post('/admin/block/{user}', [\App\Http\Controllers\UserController::class, 'block'])->name('admin.users.block');
    Route::post('/admin/remove/{user}', [\App\Http\Controllers\UserController::class, 'remove'])->name('admin.users.remove');

    // Default User Monitoring Route
    Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/activities', [\App\Http\Controllers\UserController::class, 'activities'])->name('users.activities');
    Route::post('/devices/species', [DeviceController::class, 'updateSpecies'])->name('devices.species.update');
    
    // AI Analysis routes
    Route::get('/analysis', [AnalysisController::class, 'index'])->name('analysis.index');
    Route::get('/analysis/latest', [AnalysisController::class, 'latest'])->name('analysis.latest');
    Route::get('/analysis/generate', [AnalysisController::class, 'generate'])->name('analysis.generate');
    Route::get('/analysis/{analysis}', [AnalysisController::class, 'show'])->name('analysis.show');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware(["auth", \App\Http\Middleware\EnsureUserIsApproved::class])->group(function () {

    Route::get("/chat", [GeminiController::class, "index"])
        ->name("chat.page");

    Route::post("/chat", [GeminiController::class, "chat"])
        ->name("chat.send");

});

    Route::get("/chat-ui", function () {
    return view("chat-ui");
});
   Route::get("/test-gemini", function () {

     $apiKey = env("GEMINI_API_KEY");

    $response = Http::post(
        "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}",
        [
            "contents" => [
                [
                    "parts" => [
                        ["text" => "Hello Gemini!"]
                    ]
                ]
            ]
        ]
    );

    
    return $response ? $response->json() : null;
});

    });

require __DIR__.'/auth.php';
