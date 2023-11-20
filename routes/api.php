<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

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
Route::get('/', function () {
    return response()->json(['welcome'], 201);
});

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::get('/monthly-transaction-report', [ReportController::class, 'monthlyTransactionReport']);
    Route::post('/transactions', [TransactionController::class, 'create']);
    Route::post('/payment', [PaymentController::class, 'create']);

});
