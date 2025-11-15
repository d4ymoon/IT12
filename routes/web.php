<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
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
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Admin only routes
    Route::middleware(['role:Administrator'])->group(function () {
        Route::resource('roles', RoleController::class);
        Route::resource('users', UserController::class);
        Route::resource('products', ProductController::class);
        Route::resource('suppliers', SupplierController::class);

        Route::post('/users/{user}/archive', [UserController::class, 'archive'])->name('users.archive');
        Route::post('/users/{user}/restore', [UserController::class, 'restore'])->name('users.restore');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

        Route::resource('suppliers', SupplierController::class);
        Route::post('/suppliers/{supplier}/archive', [SupplierController::class, 'archive'])->name('suppliers.archive');
        Route::post('/suppliers/{supplier}/restore', [SupplierController::class, 'restore'])->name('suppliers.restore');

        Route::resource('products', ProductController::class);
        Route::post('/products/{product}/archive', [ProductController::class, 'archive'])->name('products.archive');
        Route::post('/products/{product}/restore', [ProductController::class, 'restore'])->name('products.restore');
        Route::post('/suppliers/quick-store', [SupplierController::class, 'quickStore'])->name('suppliers.quick-store');
        Route::post('/suppliers/quick-add', [SupplierController::class, 'quickAdd'])
    ->name('suppliers.quick-add');

        Route::resource('stock-ins', StockInController::class);
        Route::get('/api/suppliers/{supplier}/products', function($supplier) {
            $supplier = App\Models\Supplier::find($supplier);
            if (!$supplier) {
                return response()->json([]);
            }
            
            $products = $supplier->products()->active()->get();
            return response()->json($products);
        });
    });

    // Both admin and employee can access these
    Route::resource('categories', CategoryController::class);
});