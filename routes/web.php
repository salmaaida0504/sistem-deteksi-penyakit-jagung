<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EdukasiController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KontenController;
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\Admin\JenisProdukController;
use App\Http\Controllers\KatalogProdukController;

/*
|--------------------------------------------------------------------------
| User-Facing Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/deteksi', [HomeController::class, 'deteksi'])->name('deteksi');
Route::get('/edukasi', [EdukasiController::class, 'index'])->name('edukasi');
Route::get('/produk', [KatalogProdukController::class, 'index'])->name('produk');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

// Auth routes (guest only)
Route::prefix('admin')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected admin routes
Route::prefix('admin')->middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Konten CRUD — using Disease model with 'konten' resource name
    Route::resource('konten', KontenController::class)->except(['show'])->names([
        'index' => 'admin.konten.index',
        'create' => 'admin.konten.create',
        'store' => 'admin.konten.store',
        'edit' => 'admin.konten.edit',
        'update' => 'admin.konten.update',
        'destroy' => 'admin.konten.destroy',
    ]);

    // Produk Pestisida CRUD
    Route::resource('produk', ProdukController::class)->except(['show'])->names([
        'index' => 'admin.produk.index',
        'create' => 'admin.produk.create',
        'store' => 'admin.produk.store',
        'edit' => 'admin.produk.edit',
        'update' => 'admin.produk.update',
        'destroy' => 'admin.produk.destroy',
    ]);

    // Jenis Produk CRUD
    Route::resource('jenis-produk', JenisProdukController::class)->except(['show'])->names([
        'index' => 'admin.jenis_produk.index',
        'create' => 'admin.jenis_produk.create',
        'store' => 'admin.jenis_produk.store',
        'edit' => 'admin.jenis_produk.edit',
        'update' => 'admin.jenis_produk.update',
        'destroy' => 'admin.jenis_produk.destroy',
    ]);
});
