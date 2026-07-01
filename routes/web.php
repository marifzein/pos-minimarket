<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Redirect Root
|--------------------------------------------------------------------------
*/




/*
|--------------------------------------------------------------------------
| Semua menu POS harus login
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/login');

Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get(
    '/dashboard',
    [DashboardController::class,'index']
        )->name('dashboard');

    //MODUL2 ROLE ADMIN 
    Route::middleware('role:Admin')->group(function () {

        // Produk
        Route::get(
            '/products',
            [ProductController::class,'index']
        );

        Route::get(
            '/products/create',
            [ProductController::class,'create']
        );

        Route::post(
            '/products',
            [ProductController::class,'store']
        );

        Route::get(
            '/products/{product}/edit',
            [ProductController::class,'edit']
        );

        Route::put(
            '/products/{product}',
            [ProductController::class,'update']
        );

        Route::get(
            '/products/{product}/stock-card',
            [ProductController::class,'stockCard']
        );

        /*
        |--------------------------------------------------------------------------
        | USER MANAGEMENT
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/users',
            [UserController::class,'index']
        )->name('users.index');

        Route::get(
            '/users/create',
            [UserController::class,'create']
        )->name('users.create');

        Route::post(
            '/users',
            [UserController::class,'store']
        )->name('users.store');

        Route::get(
            '/users/{user}/edit',
            [UserController::class,'edit']
        )->name('users.edit');

        Route::put(
            '/users/{user}',
            [UserController::class,'update']
        )->name('users.update');

        Route::post(
            '/users/{user}/reset-password',
            [UserController::class,'resetPassword']
        )->name('users.reset-password');

        // user end----------------------------------------------

    });

    // POS
    Route::get(
        '/pos',
        [PosController::class,'index']
    );

    // Search produk
    Route::get(
        '/api/products/search',
        [ProductController::class,'search']
    );

    // Simpan transaksi
    Route::post(
        '/api/transactions',
        [TransactionController::class,'store']
    );

    // Daftar transaksi
    Route::get(
        '/transactions',
        [TransactionController::class,'index']
    );

    // Detail transaksi
    Route::get(
        '/transactions/{id}',
        [TransactionController::class,'show']
    )->name('transactions.show');

    // Print transaksi
    Route::get(
        '/transactions/{id}/print',
        [TransactionController::class,'print']
    )->name('transactions.print');

    // Cek Jam
    Route::get('/cekjam', function () {

        return [
            'now_string'=>now()->format('Y-m-d H:i:s'),
            'php'=>date('Y-m-d H:i:s'),
            'timezone'=>config('app.timezone')
        ];

    });

    
    /*
    |--------------------------------------------------------------------------
    | STOCK OPNAME
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/stock-opname',
        [StockOpnameController::class,'index']
    );

    Route::post(
        '/stock-opname/start',
        [StockOpnameController::class,'start']
    );

    Route::get(
        '/stock-opname/{stockOpname}',
        [StockOpnameController::class,'show']
    );

    Route::post(
        '/stock-opname/{stockOpname}',
        [StockOpnameController::class,'store']
    );

    Route::post(
        '/stock-opname/{stockOpname}/finish',
        [StockOpnameController::class,'finish']
    );

    // cetak SO
    Route::get(
        '/stock-opname/{stockOpname}/print',
        [StockOpnameController::class,'print']
    )->name('stock-opname.print');

    /*
    |--------------------------------------------------------------------------
    | Profile (Breeze)
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/profile',
        [ProfileController::class,'edit']
    )->name('profile.edit');

    Route::patch(
        '/profile',
        [ProfileController::class,'update']
    )->name('profile.update');

    Route::delete(
        '/profile',
        [ProfileController::class,'destroy']
    )->name('profile.destroy');

});

/*
|--------------------------------------------------------------------------
| Login Register Logout
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';