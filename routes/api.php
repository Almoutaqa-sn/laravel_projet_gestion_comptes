<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CompteController;
use Illuminate\Http\Request;

Route::get('/v1/comptes', [CompteController::class, 'index']);
Route::get('/v1/comptes/{numero}', [CompteController::class, 'show']);
Route::post('/login', function (Request $request) {
    return response()->json(['message' => 'Login route ok'], 200);
})->name('login');

Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    Route::post('comptes', [CompteController::class, 'store']);
});