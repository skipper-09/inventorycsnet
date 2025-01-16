<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Master\BranchController;
use App\Http\Controllers\Master\CustomerController;
use App\Http\Controllers\Master\UnitProductController;
use App\Http\Controllers\Master\ZoneOdpController;
use App\Http\Controllers\Settings\SettingController;
use App\Http\Controllers\Transaction\IncomeProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Master\OdpController;
use App\Http\Controllers\Master\ProductController;
use App\Http\Controllers\Report\BranchProductStockController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\RoleController;
use App\Http\Controllers\Settings\UserController;
use App\Http\Controllers\Transaction\OutcomeProductController;

// Route::get('/', function () {
//     return redirect()->route('dashboard');
// });

Route::get('', function () {
    return redirect()->route('login');
});


Route::prefix('auth')->group(function () {
    Route::get('login', [AuthController::class, 'index'])->name('login')->middleware('guest');
    Route::get('reset-password', [AuthController::class, 'ResetPassword'])->name('resetpassword')->middleware('guest');
    Route::post('signin', [AuthController::class, 'signin'])->name('auth.signin');
    Route::get('signout', [AuthController::class, 'signout'])->name('auth.signout');
});

Route::prefix('admin')->middleware('auth')->group(function () {
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

        //customer
        Route::prefix('customer')->group(function () {
            Route::get('', [CustomerController::class, 'index'])->name('customer');
            Route::get('getdata', [CustomerController::class, 'getData'])->name('customer.getdata');
            Route::get('add', [CustomerController::class, 'create'])->name('customer.add');
            Route::post('store', [CustomerController::class, 'store'])->name('customer.store');
            Route::get('/edit/{id}', [CustomerController::class, 'show'])->name('customer.edit');
            Route::put('/update/{id}', [CustomerController::class, 'update'])->name('customer.update');
            Route::delete('/delete/{id}', [CustomerController::class, 'destroy'])->name('customer.delete');
            Route::get('getdataodp/{zone_id}', [CustomerController::class, 'getOdpByZone'])->name('customer.getdataodp');
        });
    });

    //income product
    Route::prefix('incomeproduct')->group(function () {
        Route::get('', [IncomeProductController::class, 'index'])->name('incomeproduct');
        Route::get('getdata', [IncomeProductController::class, 'getData'])->name('incomeproduct.getdata');
        Route::post('store', [IncomeProductController::class, 'store'])->name('incomeproduct.store');
        Route::get('/edit/{id}', [IncomeProductController::class, 'show'])->name('incomeproduct.edit');
        Route::put('/update/{id}', [IncomeProductController::class, 'update'])->name('incomeproduct.update');
        Route::delete('/delete/{id}', [IncomeProductController::class, 'destroy'])->name('incomeproduct.delete');
    });

    //outcome product
    Route::prefix('outcomeproduct')->group(function () {
        Route::get('', [OutcomeProductController::class, 'index'])->name('outcomeproduct');
        Route::get('getdata', [OutcomeProductController::class, 'getData'])->name('outcomeproduct.getdata');
        Route::post('store', [OutcomeProductController::class, 'store'])->name('outcomeproduct.store');
        Route::get('/edit/{id}', [OutcomeProductController::class, 'show'])->name('outcomeproduct.edit');
        Route::put('/update/{id}', [OutcomeProductController::class, 'update'])->name('outcomeproduct.update');
        Route::delete('/delete/{id}', [OutcomeProductController::class, 'destroy'])->name('outcomeproduct.delete');
    });

    // report
    Route::prefix('report')->group(function () {
        Route::prefix('product-stock')->group(function () {
            Route::get('', [BranchProductStockController::class, 'index'])->name('product-stock');
            Route::get('getdata', [BranchProductStockController::class, 'getData'])->name('product-stock.getdata');
        });
    });




    //settings
    Route::prefix('settings')->group(function () {
        Route::prefix('profile')->group(function () {
            Route::get('/{id}', [ProfileController::class, 'index'])->name('setting.profile');
            Route::put('/update/{id}', [ProfileController::class, 'update'])->name('setting.profile.update');
        });

        Route::prefix('user')->group(function () {
            Route::get('', [UserController::class, 'index'])->name('user');
            Route::get('getdata', [UserController::class, 'getData'])->name('user.getdata');
            Route::post('store', [UserController::class, 'store'])->name('user.store');
            Route::get('/edit/{id}', [UserController::class, 'show'])->name('user.edit');
            Route::put('/update/{id}', [UserController::class, 'update'])->name('user.update');
            Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('user.delete');
        });

        Route::prefix('role')->group(function () {
            Route::get('', [RoleController::class, 'index'])->name('role');
            Route::get('getdata', [RoleController::class, 'getData'])->name('role.getdata');
            Route::get('add', [RoleController::class, 'create'])->name('role.add');
            Route::post('store', [RoleController::class, 'store'])->name('role.store');
            Route::get('/edit/{id}', [RoleController::class, 'show'])->name('role.edit');
            Route::put('/update/{id}', [RoleController::class, 'update'])->name('role.update');
            Route::delete('/delete/{id}', [RoleController::class, 'destroy'])->name('role.delete');
        });

        Route::prefix('general')->group(function () {
            Route::get('', [SettingController::class, 'index'])->name('setting');
            Route::post('/update', [SettingController::class, 'update'])->name('setting.update');
        });
    });
});
