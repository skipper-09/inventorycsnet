<?php

use App\Http\Controllers\Master\BranchController;
use App\Http\Controllers\Master\UnitProductController;
use App\Http\Controllers\Master\ZoneOdpController;
use App\Http\Controllers\Transaction\IncomeProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Master\OdpController;
use App\Http\Controllers\Master\ProductController;
use App\Http\Controllers\Settings\UserController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::prefix('admin')->group(function () {
    Route::get('', function () {
        return redirect()->route('dashboard');
    });

    //route dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    //route master group
    Route::prefix('master')->group(function () {
        // Unit Produk
        Route::prefix('unit-produk')->group(function () {
            Route::get('', [UnitProductController::class, 'index'])->name('unitproduk');
            Route::get('getdata', [UnitProductController::class, 'GetData'])->name('unitproduk.getdata');
            Route::post('store', [UnitProductController::class, 'store'])->name('unitproduk.store');
            Route::get('/edit/{id}', [UnitProductController::class, 'show'])->name('unitproduk.edit');
            Route::put('/update/{id}', [UnitProductController::class, 'update'])->name('unitproduk.update');
            Route::delete('/delete/{id}', [UnitProductController::class, 'destroy'])->name('unitproduk.delete');
        });

        // Produk
        Route::prefix('produk')->group(function () {
            Route::get('', [ProductController::class, 'index'])->name('produk');
            Route::get('getdata', [ProductController::class, 'getData'])->name('produk.getdata');
            Route::post('store', [ProductController::class, 'store'])->name('produk.store');
            Route::get('/edit/{id}', [ProductController::class, 'show'])->name('produk.edit');
            Route::put('/update/{id}', [ProductController::class, 'update'])->name('produk.update');
            Route::delete('/delete/{id}', [ProductController::class, 'destroy'])->name('produk.delete');
        });

        // branch
        Route::prefix('cabang')->group(function () {
            Route::get('', [BranchController::class, 'index'])->name('branch');
            Route::get('getdata', [BranchController::class, 'getData'])->name('branch.getdata');
            Route::post('store', [BranchController::class, 'store'])->name('branch.store');
            Route::get('/edit/{id}', [BranchController::class, 'show'])->name('branch.edit');
            Route::put('/update/{id}', [BranchController::class, 'update'])->name('branch.update');
            Route::delete('/delete/{id}', [BranchController::class, 'destroy'])->name('branch.delete');
        });

        // zoneodp
        Route::prefix('jalur')->group(function () {
            Route::get('', [ZoneOdpController::class, 'index'])->name('zone');
            Route::get('getdata', [ZoneOdpController::class, 'getData'])->name('zone.getdata');
            Route::get('syncdata', [ZoneOdpController::class, 'SyncData'])->name('zone.syncdata');
            Route::post('store', [ZoneOdpController::class, 'store'])->name('zone.store');
            // Route::get('/edit/{id}', [ZoneOdpController::class, 'show'])->name('zone.edit');
            // Route::put('/update/{id}', [ZoneOdpController::class, 'update'])->name('zone.update');
            Route::delete('/delete/{id}', [ZoneOdpController::class, 'destroy'])->name('zone.delete');
        });

        //odp
        Route::prefix('odp')->group(function () {
            Route::get('', [OdpController::class, 'index'])->name('odp');
            Route::get('getdata', [OdpController::class, 'getData'])->name('odp.getdata');
            Route::get('syncdata', [OdpController::class, 'SyncData'])->name('odp.syncdata');
            Route::post('store', [OdpController::class, 'store'])->name('odp.store');
            // Route::get('/edit/{id}', [OdpController::class, 'show'])->name('odp.edit');
            // Route::put('/update/{id}', [OdpController::class, 'update'])->name('odp.update');
            Route::delete('/delete/{id}', [OdpController::class, 'destroy'])->name('odp.delete');
        });
    });
    
    //income product
    Route::prefix('pemasukan-barang')->group(function () {
        Route::get('', [IncomeProductController::class, 'index'])->name('incomeproduct');
        Route::get('getdata', [IncomeProductController::class, 'getData'])->name('incomeproduct.getdata');
        Route::post('store', [IncomeProductController::class, 'store'])->name('incomeproduct.store');
        Route::get('/edit/{id}', [IncomeProductController::class, 'show'])->name('incomeproduct.edit');
        Route::put('/update/{id}', [IncomeProductController::class, 'update'])->name('incomeproduct.update');
        Route::delete('/delete/{id}', [IncomeProductController::class, 'destroy'])->name('incomeproduct.delete');
    });




    //settings
    Route::prefix('settings')->group(function () {
        Route::prefix('user')->group(function () {
            Route::get('', [UserController::class, 'index'])->name('user');
            Route::get('getdata', [UserController::class, 'getData'])->name('user.getdata');
            Route::post('store', [UserController::class, 'store'])->name('user.store');
            Route::get('/edit/{id}', [UserController::class, 'show'])->name('user.edit');
            Route::put('/update/{id}', [UserController::class, 'update'])->name('user.update');
            Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('user.delete');
        });
    });
});
