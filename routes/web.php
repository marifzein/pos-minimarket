<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\ProductImportController;
use App\Http\Controllers\DeveloperController;

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
    /*
    |--------------------------------------------------------------------------
    | MASTER DATA
    |--------------------------------------------------------------------------
    */
        // supplier
        Route::resource(
            'suppliers', 
            SupplierController::class
        )->except([
            'show',
            'destroy'
        ]);

        // Produk
        Route::resource(
            'products',
            ProductController::class
        )->except([
            'show',
            'destroy'
        ]);

        // PO
        Route::resource(
            'purchasing',
            PurchaseOrderController::class
        )->except([
            'show',
            'destroy'
        ]);
        
        // User
        Route::resource(
            'users',
            UserController::class
        )->except([
            'show',
            'destroy'
        ]);

        // Customers
        Route::resource('customers', CustomerController::class);

        // kategori
        Route::resource(
            'categories',
            CategoryController::class
        )->except([
            'show',
            'destroy'
        ]);
        

    });

    /*
    |--------------------------------------------------------------------------
    | TRANSAKSI
    |--------------------------------------------------------------------------
    */

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
    ============================================================|
    RESET DAN SEEDING BUAT TEST
    ============================================================|
    */
    

    Route::prefix('developer')
        ->name('developer.')
        ->group(function () {

            Route::get(
                '/',
                [DeveloperController::class,'index']
            )->name('index');

            Route::post(
                '/reset-transaksi',
                [DeveloperController::class,'resetTransaksi']
            )->name('reset.transaksi');

            Route::post(
                '/reset-master',
                [DeveloperController::class,'resetMaster']
            )->name('reset.master');

            Route::post(
                '/seed',
                [DeveloperController::class,'seedDemo']
            )->name('seed');

        });





    /* 
    ============================================================|  
    BACKUP DB
    ============================================================|  
    */  
    Route::prefix('backup')
    ->group(function () {

        Route::get(
            '/',
            [BackupController::class,'index']
        )->name('backup.index');

        Route::post(
            '/create',
            [BackupController::class,'backup']
        )->name('backup.create');

        Route::get(
            '/download/{file}',
            [BackupController::class,'download']
        )->name('backup.download');

        Route::delete(
            '/delete/{file}',
            [BackupController::class,'destroy']
        )->name('backup.destroy');

    });
    /*
    IMPORT PRODUK VIA FILE EXCELL
    */
    

    Route::prefix('products')
        ->group(function () {

            Route::get(
                '/import',
                [ProductImportController::class,'index']
            )->name('products.import');

            Route::post(
                '/import',
                [ProductImportController::class,'import']
            )->name('products.import.store');

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