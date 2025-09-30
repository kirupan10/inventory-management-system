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
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

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
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
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
    Route::get('/orders/{order}/download-pdf-bill', [OrderController::class, 'downloadPdfBill'])->name('orders.download-pdf-bill');
    Route::get('/orders/{order}/receipt', [OrderController::class, 'showReceipt'])->name('orders.receipt');

    // Letterhead Configuration Routes
    Route::get('/letterhead', [App\Http\Controllers\LetterheadController::class, 'index'])->name('letterhead.index');
    Route::post('/letterhead/upload', [App\Http\Controllers\LetterheadController::class, 'uploadLetterhead'])->name('letterhead.upload');
    Route::post('/letterhead/save-positions', [App\Http\Controllers\LetterheadController::class, 'savePositions'])->name('letterhead.save-positions');
    Route::get('/letterhead/positions', [App\Http\Controllers\LetterheadController::class, 'getPositions'])->name('letterhead.get-positions');
    Route::post('/letterhead/save-toggles', [App\Http\Controllers\LetterheadController::class, 'saveToggles'])->name('letterhead.save-toggles');
    Route::get('/letterhead/toggles', [App\Http\Controllers\LetterheadController::class, 'getToggles'])->name('letterhead.get-toggles');
    Route::post('/letterhead/save-items-alignment', [App\Http\Controllers\LetterheadController::class, 'saveItemsAlignment'])->name('letterhead.save-items-alignment');
    Route::post('/letterhead/regenerate-preview', [App\Http\Controllers\LetterheadController::class, 'regeneratePreview'])->name('letterhead.regenerate-preview');

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

// Price debug route
Route::get('/price-debug', function() {
    return view('price-debug');
})->name('price.debug');

// Debug route for PDF testing - Direct approach
Route::get('/test-pdf', function () {
    $order = \App\Models\Order::with(['customer', 'details.product'])->first();
    if (!$order) {
        return response('No orders found. Create an order first.', 404);
    }

    $pdf = PDF::loadView('orders.pdf-bill', [
        'order' => $order,
        'letterheadConfig' => [],
    ]);
    return $pdf->stream('test-invoice-' . $order->invoice_no . '.pdf');
});

// Debug route that mimics OrderController exactly
Route::get('/test-order-controller/{id?}', function ($id = null) {
    $order = $id ? \App\Models\Order::findOrFail($id) : \App\Models\Order::with(['customer', 'details.product'])->first();
    if (!$order) {
        return response('No orders found. Create an order first.', 404);
    }

    // Load the order with its relationships (exactly like OrderController)
    $order->loadMissing(['customer', 'details.product']);

    // Get letterhead config (exactly like OrderController)
    $letterheadConfig = [];
    if (file_exists(storage_path('app/letterhead_config.json'))) {
        $letterheadConfig = json_decode(file_get_contents(storage_path('app/letterhead_config.json')), true) ?? [];
    }

    // Generate PDF using DomPDF (exactly like OrderController generateStandardPdf)
    $pdf = PDF::loadView('orders.pdf-bill', [
        'order' => $order,
        'letterheadConfig' => $letterheadConfig,
    ]);

    // Set paper size to A4 and orientation to portrait (exactly like OrderController)
    $pdf->setPaper('A4', 'portrait');

    // Set options for better rendering (exactly like OrderController)
    $pdf->setOptions([
        'dpi' => 72, // Match the positioning canvas DPI
        'defaultFont' => 'Arial',
        'isHtml5ParserEnabled' => true,
        'isPhpEnabled' => true,
        'isRemoteEnabled' => true,
    ]);

    return $pdf->stream('controller-test-' . $order->invoice_no . '.pdf');
});

// Debug calculation route
Route::get('/debug-calculations/{order?}', function($orderId = null) {
    if ($orderId) {
        $order = Order::with(['details.product', 'customer'])->find($orderId);
        if (!$order) {
            return view('debug-calculations')->with('error', 'Order not found');
        }
        return view('debug-calculations', compact('order'));
    }

    // Show latest order if no ID provided
    $order = Order::with(['details.product', 'customer'])->latest()->first();
    return view('debug-calculations', compact('order'));
})->name('debug.calculations');
