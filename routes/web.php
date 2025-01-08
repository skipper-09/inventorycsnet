<?php

use App\Http\Controllers\Master\UnitProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\DashboardController;

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
        //unit produk
        Route::prefix('unit-produk')->group(function () {
            Route::get('', [UnitProductController::class, 'index'])->name('unitproduk');
            Route::get('getdata', [UnitProductController::class, 'GetData'])->name('unitproduk.getdata');
            Route::post('store', [UnitProductController::class, 'store'])->name('unitproduk.store');
            Route::get('/edit/{id}', [UnitProductController::class, 'show'])->name('unitproduk.edit');
            Route::put('/update/{id}', [UnitProductController::class, 'update'])->name('unitproduk.update');
            Route::delete('/delete/{id}', [UnitProductController::class, 'destroy'])->name('unitproduk.delete');
        });
    });
});