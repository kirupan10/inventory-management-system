<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;

use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Dashboards\DashboardController;
use App\Http\Controllers\Product\ProductExportController;
use App\Http\Controllers\Product\ProductImportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('php/', function () {
    return phpinfo();
});

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User Management
    Route::resource('/users', UserController::class); //->except(['show']);
    Route::put('/user/change-password/{username}', [UserController::class, 'updatePassword'])->name('users.updatePassword');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/settings', [ProfileController::class, 'settings'])->name('profile.settings');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('/customers', CustomerController::class);
    Route::resource('/categories', CategoryController::class);
    Route::resource('/units', UnitController::class);

    // Payment routes
    Route::post('/payment/modal', [\App\Http\Controllers\PaymentController::class, 'modal'])->name('payment.modal');

    // Route Products
    Route::get('/products/import', [ProductImportController::class, 'create'])->name('products.import.view');
    Route::post('/products/import', [ProductImportController::class, 'store'])->name('products.import.store');
    Route::get('/products/export', [ProductExportController::class, 'create'])->name('products.export.store');
    Route::resource('/products', ProductController::class);

    // Route Orders - Simplified
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders/store', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::put('/orders/update/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    Route::get('/orders/details/{order_id}/download', [OrderController::class, 'downloadInvoice'])->name('order.downloadInvoice');

});

require __DIR__.'/auth.php';

Route::get('test/', function (){
//    return view('test');
    return view('orders.create');
});

Route::post('simple-test', function(\Illuminate\Http\Request $request) {
    // Log all request data
    \Log::info('Simple test route hit', [
        'all_data' => $request->all(),
        'customer_id' => $request->customer_id,
        'payment_type' => $request->payment_type,
        'cart_items' => $request->cart_items,
        'method' => $request->method(),
        'url' => $request->url(),
        'ip' => $request->ip()
    ]);

    return response('<h1>SUCCESS!</h1><pre>' . json_encode($request->all(), JSON_PRETTY_PRINT) . '</pre>');
})->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
