<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\AuthController;


Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->get('/me', [AuthController::class, 'me']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
    Route::middleware('auth:sanctum')->get('/users', [AuthController::class, 'index']); // Rute untuk mendapatkan semua pengguna
});
    /*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register API routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | is assigned the "api" middleware group. Enjoy building your API!
    |
    */

    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });

    // Payment Routes
    Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('payments', PaymentController::class)->only(['index', 'store', 'show', 'update', 'history']);
});


    // Vehicle Routes
    Route::prefix('vehicles')->group(function () {
        Route::get('/', [VehicleController::class, 'index']);
        Route::post('/', [VehicleController::class, 'store']);
        Route::get('/{id}', [VehicleController::class, 'show']);
        Route::put('/{id}', [VehicleController::class, 'update']);
        Route::delete('/{id}', [VehicleController::class, 'destroy']);
    });

 // Credit Routes
Route::middleware('auth:sanctum')->prefix('credits')->group(function () {
    Route::get('/', [CreditController::class, 'index']);     // hanya user login yg bisa lihat
    Route::post('/', [CreditController::class, 'store']);    // user login otomatis jadi pemilik
    Route::get('/{id}', [CreditController::class, 'show']);
    Route::put('/{id}', [CreditController::class, 'update']);
    Route::get('/summary', [CreditController::class, 'summary']);

});


