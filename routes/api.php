<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ImageStorageController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Image Storage API Routes
Route::prefix('images')->name('api.images.')->group(function () {
    Route::post('/', [ImageStorageController::class, 'store'])->name('store');
    Route::get('/', [ImageStorageController::class, 'index'])->name('index');
    Route::get('/{filename}', [ImageStorageController::class, 'show'])->name('show');
    Route::delete('/{filename}', [ImageStorageController::class, 'destroy'])->name('destroy');
});

// Health check for API
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'api' => 'Bokod Medical CMS Image API',
        'timestamp' => now()->toISOString()
    ]);
});