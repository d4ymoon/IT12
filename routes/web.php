<?php

use App\Http\Controllers\AccountSettingsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductPriceController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinancialReportController;
use App\Http\Controllers\InventoryReportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\StockAdjustmentController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes - require login
Route::middleware(['auth.simple'])->group(function () {
    
    // Admin only routes - use the new 'admin' middleware
    Route::middleware(['role:Administrator'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/sales-data', [DashboardController::class, 'getSalesData'])->name('dashboard.sales-data');
        Route::get('/dashboard/sales-chart-data', [DashboardController::class, 'getSalesChartData']);
        
        Route::resource('roles', RoleController::class);
        Route::resource('users', UserController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('products', ProductController::class);
        Route::resource('suppliers', SupplierController::class);

        Route::post('/users/{user}/archive', [UserController::class, 'archive'])->name('users.archive');
        Route::post('/users/{user}/restore', [UserController::class, 'restore'])->name('users.restore');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

        Route::post('/suppliers/{supplier}/archive', [SupplierController::class, 'archive'])->name('suppliers.archive');
        Route::post('/suppliers/{supplier}/restore', [SupplierController::class, 'restore'])->name('suppliers.restore');

        Route::post('/products/{product}/archive', [ProductController::class, 'archive'])->name('products.archive');
        Route::post('/products/{product}/restore', [ProductController::class, 'restore'])->name('products.restore');
        Route::get('/products/suggest-sku/{categoryId}', [ProductController::class, 'suggestSku']);
        Route::post('/suppliers/quick-store', [SupplierController::class, 'quickStore'])->name('suppliers.quick-store');
        Route::post('/suppliers/quick-add', [SupplierController::class, 'quickAdd'])->name('suppliers.quick-add');

        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/sales', [SalesReportController::class, 'index'])->name('reports.sales.index');
        Route::get('/reports/inventory', [InventoryReportController::class, 'index'])->name('reports.inventory.index');
        Route::get('/reports/financial', [FinancialReportController::class, 'index'])->name('reports.financial.index');
        Route::get('/reports/export-pdf/{module}', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');

        Route::resource('stock-ins', StockInController::class);
        Route::get('/api/suppliers/{supplier}/products', function($supplier) {
            $supplier = App\Models\Supplier::find($supplier);
            if (!$supplier) {
                return response()->json([]);
            }
            
            $products = $supplier->products()->active()->get();
            return response()->json($products);
        });

        Route::resource('stock-adjustments', StockAdjustmentController::class);
        Route::get('stock-adjustments/{id}/show', [StockAdjustmentController::class, 'show'])->name('stock-adjustments.show');
        
        Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
        Route::get('/sales/{id}', [SaleController::class, 'show'])->name('sales.show');
        Route::get('/sales/{id}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');
        Route::get('/sales/{id}/details', [SaleController::class, 'details'])->name('sales.details');

        Route::get('/product-prices', [ProductPriceController::class, 'index'])->name('product-prices.index');
        Route::post('/product-prices/update', [ProductPriceController::class, 'update'])->name('product-prices.update');
        Route::get('/api/product-prices/{product}/history', [ProductPriceController::class, 'priceHistory']);

        Route::resource('returns', ReturnController::class);
        Route::get('/returns/get-sale/{saleId}', [ReturnController::class, 'getSaleDetails'])->name('returns.get-sale-details');
    });

    // Both admin and employee can access these
    Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
    Route::post('/clear-password-modal-flag', function() {
        session()->forget('show_default_password_modal');
        return response()->json(['success' => true]);
    })->name('clear-password-modal-flag');

    // Shared POS functionality routes
    Route::prefix('pos')->group(function () {
        Route::post('/initialize-sale', [POSController::class, 'initializeSale']);
        Route::post('/search-product', [POSController::class, 'searchProduct']);
        Route::post('/add-item', [POSController::class, 'addItem']);
        Route::put('/update-item/{itemId}', [POSController::class, 'updateItem']);
        Route::delete('/remove-item/{itemId}', [POSController::class, 'removeItem']);
        Route::get('/sale-items/{saleId}', [POSController::class, 'getSaleItems']);
        Route::post('/process-payment', [POSController::class, 'processPayment']);
        Route::get('/receipt/{sale}/pdf', [POSController::class, 'downloadReceiptPDF']);
        Route::post('/complete-sale', [POSController::class, 'completeSale'])->name('pos.completeSale');
    });

    // Account settings - both can access
    Route::get('/account/settings', [AccountSettingsController::class, 'edit'])->name('account.settings');
    Route::put('/account/settings', [AccountSettingsController::class, 'update'])->name('account.settings.update');
    Route::put('/account/settings/password', [AccountSettingsController::class, 'updatePassword'])->name('account.settings.password');
});