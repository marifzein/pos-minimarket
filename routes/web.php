<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StockOpnameController;


Route::get('/', function () {
    return redirect('/dashboard');
});

// buka hal POS
Route::get('/pos', [PosController::class, 'index']);

Route::get('/api/products/search', [
    ProductController::class,
    'search'
]);

// save transaksi/nota
Route::post(
    '/api/transactions',
    [TransactionController::class, 'store']
);

// daftar nota/transaksi
Route::get(
    '/transactions',
    [TransactionController::class, 'index']
);  

// tampilkan nota detail
Route::get(
    '/transactions/{id}',
    [TransactionController::class, 'show']
)->name('transactions.show');

// cetak struk
Route::get(
    '/transactions/{id}/print',
    [TransactionController::class, 'print']
)->name('transactions.print');

// cek jam hrs sesuai dgn WIB
Route::get('/cekjam', function () {

    return [
        'now_string' => now()->format('Y-m-d H:i:s'),
        'php' => date('Y-m-d H:i:s'),
        'timezone' => config('app.timezone')
    ];

});

// dashboard
Route::get(
    '/dashboard',
    [DashboardController::class, 'index']
);

// ploduk2 dalam negeli
Route::get(
    '/products',
    [ProductController::class, 'index']
);

// form tambah produk
Route::get(
    '/products/create',
    [ProductController::class, 'create']
);

// simpan produk
Route::post(
    '/products',
    [ProductController::class, 'store']
);

// halaman edit produk
Route::get(
    '/products/{product}/edit',
    [ProductController::class, 'edit']
);

//update produk
Route::put(
    '/products/{product}',
    [ProductController::class, 'update']
);

// kartu stok
Route::get(
    '/products/{product}/stock-card',
    [ProductController::class, 'stockCard']
);

// ===============================
// STOCK OPNAME
// ===============================

// daftar SO
Route::get(
    '/stock-opname',
    [StockOpnameController::class,'index']
);

// mulai SO baru
Route::post(
    '/stock-opname/start',
    [StockOpnameController::class,'start']
);

// halaman detail SO
Route::get(
    '/stock-opname/{stockOpname}',
    [StockOpnameController::class,'show']
);

// scan / simpan item
Route::post(
    '/stock-opname/{stockOpname}',
    [StockOpnameController::class,'store']
);

// posting selesai
Route::post(
    '/stock-opname/{stockOpname}/finish',
    [StockOpnameController::class,'finish']
);