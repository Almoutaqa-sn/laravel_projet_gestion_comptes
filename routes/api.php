<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CompteController;
use Illuminate\Http\Request;

Route::middleware(['api', \App\Http\Middleware\LoggingMiddleware::class])->group(function () {
    Route::get('/v1/comptes', [CompteController::class, 'index']);
    Route::post('/v1/comptes', [CompteController::class, 'store']);
    Route::get('/v1/comptes/{numero}', [CompteController::class, 'show']);
});

Route::middleware(['api'])->group(function () {
    Route::get('/health', function () {
        \Log::info('Health check accessed');
        try {
            \DB::connection()->getPdo();
            return response()->json([
                'status' => 'ok',
                'database' => 'connected',
                'timestamp' => now()
            ]);
        } catch (\Exception $e) {
            \Log::error('Health check failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'database' => 'disconnected',
                'error' => $e->getMessage()
            ], 500);
        }
    });
});

Route::post('/login', function (Request $request) {
    \Log::info('Login route accessed');
    return response()->json(['message' => 'Login route ok'], 200);
})->name('login');

// Route de test de santé
Route::get('/health', function () {
    \Log::info('Health check accessed');
    try {
        \DB::connection()->getPdo();
        return response()->json([
            'status' => 'ok',
            'database' => 'connected',
            'timestamp' => now()
        ]);
    } catch (\Exception $e) {
        \Log::error('Health check failed: ' . $e->getMessage());
        return response()->json([
            'status' => 'error',
            'database' => 'disconnected',
            'error' => $e->getMessage()
        ], 500);
    }
});

Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    Route::post('comptes', [CompteController::class, 'store']);
});