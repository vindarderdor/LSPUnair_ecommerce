<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - E-Commerce
|--------------------------------------------------------------------------
*/

// =======================
// RUTE FRONTEND (TOKO)
// =======================
Route::get('/', [ShopController::class, 'index'])->name('shop.index');
Route::get('/produk/{id}', [ShopController::class, 'detail'])->name('shop.detail');

// Rute Keranjang Belanja (Shopping Cart)
Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
Route::post('/keranjang/tambah/{id}', [CartController::class, 'add'])->name('cart.add');
Route::post('/keranjang/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::post('/keranjang/hapus/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/keranjang/kosongkan', [CartController::class, 'clear'])->name('cart.clear');

// Rute Pembeli (Memerlukan Login)
Route::middleware('auth')->group(function () {
    Route::get('/pesanan-saya', [ShopController::class, 'myOrders'])->name('shop.orders');
    Route::post('/pesan/{id}', [OrderController::class, 'checkout'])->name('order.checkout');
    Route::post('/pesanan/{id}/bukti', [OrderController::class, 'uploadBukti'])->name('order.upload-bukti');
    Route::post('/keranjang/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
});

// =======================
// RUTE AUTENTIKASI
// =======================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Logout
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// =======================
// RUTE ADMIN (ADMINLTE)
// Khusus Penjual yang telah Login
// =======================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:penjual'])->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/produk', [AdminController::class, 'produk'])->name('produk.index');
    Route::get('/produk/tambah', [AdminController::class, 'createProduk'])->name('produk.create');
    Route::post('/produk', [AdminController::class, 'storeProduk'])->name('produk.store');
    Route::get('/produk/{id}/edit', [AdminController::class, 'editProduk'])->name('produk.edit');
    Route::put('/produk/{id}', [AdminController::class, 'updateProduk'])->name('produk.update');
    Route::delete('/produk/{id}', [AdminController::class, 'destroyProduk'])->name('produk.destroy');
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders.index');
    Route::put('/orders/{id}/status', [AdminController::class, 'updateOrderStatus'])->name('orders.update-status');
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::put('/users/{id}/role', [AdminController::class, 'updateRole'])->name('users.update-role');
});
