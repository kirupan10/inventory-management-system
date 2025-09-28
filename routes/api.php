<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\ProductController;

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

Route::get('products/', [ProductController::class, 'index'])->name('api.product.index');

// Payment routes
Route::post('payments/process', [\App\Http\Controllers\API\V1\PaymentController::class, 'processPayment'])->name('api.payment.process');
Route::get('orders/{order}/payment', [\App\Http\Controllers\API\V1\PaymentController::class, 'show'])->name('api.payment.show');
