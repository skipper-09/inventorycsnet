<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Master\AllowanceController;
use App\Http\Controllers\Master\AllowanceTypeController;
use App\Http\Controllers\Master\AssigmentDataController;
use App\Http\Controllers\Master\AssignmentController;
use App\Http\Controllers\Master\BranchController;
use App\Http\Controllers\Master\CustomerController;
use App\Http\Controllers\Master\DeductionController;
use App\Http\Controllers\Master\DeductionTypeController;
use App\Http\Controllers\Master\DepartmentController;
use App\Http\Controllers\Master\EmployeeController;
use App\Http\Controllers\Master\FormTemplateBuilderController;
use App\Http\Controllers\Master\PositionController;
use App\Http\Controllers\Master\ProductRoleController;
use App\Http\Controllers\Master\SalaryController;
use App\Http\Controllers\Master\TaskDataController;
use App\Http\Controllers\Master\TaskDetailController;
use App\Http\Controllers\Master\TaskTemplateController;
use App\Http\Controllers\Master\UnitProductController;
use App\Http\Controllers\Master\ZoneOdpController;
use App\Http\Controllers\Report\LeaveReportController;
use App\Http\Controllers\Report\TaskReportController;
use App\Http\Controllers\Settings\SettingController;
use App\Http\Controllers\Submission\LeaveController;
use App\Http\Controllers\Transaction\IncomeProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Master\OdpController;
use App\Http\Controllers\Master\ProductController;
use App\Http\Controllers\Report\BranchProductStockController;
use App\Http\Controllers\Report\TransactionProductController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\RoleController;
use App\Http\Controllers\Settings\UserController;
use App\Http\Controllers\Transaction\OutcomeProductController;
use App\Http\Controllers\Transaction\TransferProductController;
use App\Http\Controllers\Transaction\WorkProductController;

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
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('can:read-dashboard');

    //route master group
    Route::prefix('master')->group(function () {
        // Unit Produk
        Route::prefix('unit-produk')->group(function () {
            Route::get('', [UnitProductController::class, 'index'])->name('unitproduk')->middleware('can:read-unit-product');
            Route::get('getdata', [UnitProductController::class, 'GetData'])->name('unitproduk.getdata');
            Route::post('store', [UnitProductController::class, 'store'])->name('unitproduk.store');
            Route::get('/edit/{id}', [UnitProductController::class, 'show'])->name('unitproduk.edit');
            Route::put('/update/{id}', [UnitProductController::class, 'update'])->name('unitproduk.update')->middleware('can:update-unit-product');
            Route::delete('/delete/{id}', [UnitProductController::class, 'destroy'])->name('unitproduk.delete')->middleware('can:delete-unit-product');
        });

        // Produk
        Route::prefix('produk')->group(function () {
            Route::get('', [ProductController::class, 'index'])->name('produk')->middleware('can:read-product');
            Route::get('getdata', [ProductController::class, 'getData'])->name('produk.getdata');
            Route::post('store', [ProductController::class, 'store'])->name('produk.store');
            Route::get('/edit/{id}', [ProductController::class, 'show'])->name('produk.edit')->middleware('can:update-product');
            Route::put('/update/{id}', [ProductController::class, 'update'])->name('produk.update');
            Route::delete('/delete/{id}', [ProductController::class, 'destroy'])->name('produk.delete')->middleware('can:delete-product');
        });

        // branch
        Route::prefix('cabang')->group(function () {
            Route::get('', [BranchController::class, 'index'])->name('branch')->middleware('can:read-branch');
            ;
            Route::get('getdata', [BranchController::class, 'getData'])->name('branch.getdata');
            Route::post('store', [BranchController::class, 'store'])->name('branch.store');
            Route::get('/edit/{id}', [BranchController::class, 'show'])->name('branch.edit')->middleware('can:update-branch');
            Route::put('/update/{id}', [BranchController::class, 'update'])->name('branch.update');
            Route::delete('/delete/{id}', [BranchController::class, 'destroy'])->name('branch.delete')->middleware('can:delete-branch');
        });

        // zoneodp
        Route::prefix('jalur')->group(function () {
            Route::get('', [ZoneOdpController::class, 'index'])->name('zone')->middleware('can:read-zone');
            Route::get('getdata', [ZoneOdpController::class, 'getData'])->name('zone.getdata');
            Route::get('syncdata', [ZoneOdpController::class, 'SyncData'])->name('zone.syncdata');
            Route::post('store', [ZoneOdpController::class, 'store'])->name('zone.store');
            Route::delete('/delete/{id}', [ZoneOdpController::class, 'destroy'])->name('zone.delete')->middleware('can:delete-zone');
        });

        //odp
        Route::prefix('odp')->group(function () {
            Route::get('', [OdpController::class, 'index'])->name('odp')->middleware('can:read-zone-odp');
            Route::get('getdata', [OdpController::class, 'getData'])->name('odp.getdata');
            Route::get('syncdata', [OdpController::class, 'SyncData'])->name('odp.syncdata');
            Route::post('store', [OdpController::class, 'store'])->name('odp.store');
            // Route::get('/edit/{id}', [OdpController::class, 'show'])->name('odp.edit');
            // Route::put('/update/{id}', [OdpController::class, 'update'])->name('odp.update');
            Route::delete('/delete/{id}', [OdpController::class, 'destroy'])->name('odp.delete')->middleware('can:delete-zone-odp');
        });

        //product role
        Route::prefix('product-role')->group(function () {
            Route::get('', [ProductRoleController::class, 'index'])->name('productrole')->middleware('can:read-product-role');
            Route::get('getdata', [ProductRoleController::class, 'getData'])->name('productrole.getdata');
            // Route::post('store', [ProductRoleController::class, 'store'])->name('productrole.store');
            Route::get('/edit/{id}', [ProductRoleController::class, 'show'])->name('productrole.edit')->middleware('can:update-product-role');
            Route::put('/update/{id}', [ProductRoleController::class, 'update'])->name('productrole.update');
            Route::delete('/delete/{id}', [ProductRoleController::class, 'destroy'])->name('productrole.delete')->middleware('can:delete-product-role');
        });

        Route::prefix('form-builder')->group(function () {
            Route::get('', [FormTemplateBuilderController::class, 'index'])->name('formbuilder');
            Route::get('getdata', [FormTemplateBuilderController::class, 'getData'])->name('formbuilder.getdata');
            Route::get('add', [FormTemplateBuilderController::class, 'create'])->name('formbuilder.add');
            Route::post('store', [FormTemplateBuilderController::class, 'store'])->name('formbuilder.store');
            Route::get('/template/{id}', [FormTemplateBuilderController::class, 'FormView'])->name('formbuilder.detail');
            Route::get('/edit/{id}', [FormTemplateBuilderController::class, 'show'])->name('formbuilder.edit');
            Route::put('/update/{id}', [FormTemplateBuilderController::class, 'update'])->name('formbuilder.update');
            Route::delete('/delete/{id}', [FormTemplateBuilderController::class, 'destroy'])->name('formbuilder.delete');
            // Route::get('getdataodp/{zone_id}', [CustomerController::class, 'getOdpByZone'])->name('customer.getdataodp');
        });

        //TaskTemmplate
        Route::prefix('task-template')->group(function () {
            Route::get('', [TaskTemplateController::class, 'index'])->name('tasktemplate')->middleware('can:read-task-template');
            Route::get('getdata', [TaskTemplateController::class, 'getData'])->name('tasktemplate.getdata');
            Route::post('store', [TaskTemplateController::class, 'store'])->name('tasktemplate.store');
            // Route::get('/detail/{slug}', [TaskTemplateController::class, 'detail'])->name('tasktemplate.detail');
            Route::get('/edit/{id}', [TaskTemplateController::class, 'show'])->name('tasktemplate.edit')->middleware('can:update-task-template');
            Route::put('/update/{id}', [TaskTemplateController::class, 'update'])->name('tasktemplate.update');
            Route::delete('/delete/{id}', [TaskTemplateController::class, 'destroy'])->name('tasktemplate.delete')->middleware('can:delete-task-template');
        });


        Route::prefix('task-data')->group(function () {
            Route::get('', [TaskDataController::class, 'index'])->name('taskdata')->middleware('can:read-task');
            Route::get('getdata', [TaskDataController::class, 'getData'])->name('taskdata.getdata');
            Route::post('store', [TaskDataController::class, 'store'])->name('taskdata.store');
            Route::get('/detail/{id}', [TaskDataController::class, 'detail'])->name('taskdata.detail')->middleware('can:read-detail-task');
            Route::get('/edit/{id}', [TaskDataController::class, 'show'])->name('taskdata.edit')->middleware('can:update-task');
            Route::put('/update/{id}', [TaskDataController::class, 'update'])->name('taskdata.update');
            Route::delete('/delete/{id}', [TaskDataController::class, 'destroy'])->name('taskdata.delete')->middleware('can:delete-task');
        });

        // Task detail
        Route::prefix('detail-task')->group(function () {
            Route::get('{taskdataid}', [TaskDetailController::class, 'index'])->name('taskdetail.index')->middleware('can:read-detail-task');
            Route::get('getdata/{taskdataid}', [TaskDetailController::class, 'getData'])->name('taskdetail.getdata');
            Route::get('add/{taskdataid}', [TaskDetailController::class, 'create'])->name('taskdetail.add')->middleware('can:create-detail-task');
            Route::post('store/{taskdataid}', [TaskDetailController::class, 'store'])->name('taskdetail.store');
            Route::get('edit/{id}', [TaskDetailController::class, 'edit'])->name('taskdetail.edit')->middleware('can:update-detail-task');
            Route::put('update/{id}', [TaskDetailController::class, 'update'])->name('taskdetail.update');
            Route::delete('delete/{id}', [TaskDetailController::class, 'destroy'])->name('taskdetail.delete')->middleware('can:delete-detail-task');
        });


        //assignment
        Route::prefix('assignment')->group(function () {
            Route::get('', [AssignmentController::class, 'index'])->name('assignment')->middleware('can:read-assignment');
            Route::get('getdata', [AssignmentController::class, 'getData'])->name('assignment.getdata');
            Route::get('add', [AssignmentController::class, 'create'])->name('assignment.add')->middleware('can:create-assignment');
            Route::post('store', [AssignmentController::class, 'store'])->name('assignment.store');
            Route::get('/edit/{id}', [AssignmentController::class, 'show'])->name('assignment.edit')->middleware('can:update-assignment');
            ;
            Route::put('/update/{id}', [AssignmentController::class, 'update'])->name('assignment.update');
            Route::delete('/delete/{id}', [AssignmentController::class, 'destroy'])->name('assignment.delete')->middleware('can:delete-assignment');
            ;
        });

        //assignment Data
        Route::prefix('assignment-data')->group(function () {
            Route::get('', [AssigmentDataController::class, 'index'])->name('assigmentdata')->middleware('can:read-assigmentdata');
            Route::get('getdata', [AssigmentDataController::class, 'getData'])->name('assigmentdata.getdata');
            // Route::get('add', [AssigmentDataController::class, 'create'])->name('assigmentdata.add')->middleware('can:create-assigment');
            // Route::post('store', [AssigmentDataController::class, 'store'])->name('assigmentdata.store');
            Route::get('/report/{id}', [AssigmentDataController::class, 'show'])->name('assigmentdata.edit')->middleware('can:update-assigmentdata');
            ;
            Route::put('/update/{id}', [AssigmentDataController::class, 'update'])->name('assigmentdata.update');
            Route::delete('/delete/{id}', [AssigmentDataController::class, 'destroy'])->name('assigmentdata.delete')->middleware('can:delete-assigmentdata');
            ;
        });

        // Deduction
        Route::prefix('deduction')->group(function () {
            Route::get('', [DeductionController::class, 'index'])->name('deduction')->middleware('can:read-deduction');
            Route::get('getdata', [DeductionController::class, 'getData'])->name('deduction.getdata');
            Route::post('store', [DeductionController::class, 'store'])->name('deduction.store');
            Route::get('/edit/{id}', [DeductionController::class, 'show'])->name('deduction.edit')->middleware('can:update-deduction');
            Route::put('/update/{id}', [DeductionController::class, 'update'])->name('deduction.update');
            Route::delete('/delete/{id}', [DeductionController::class, 'destroy'])->name('deduction.delete')->middleware('can:delete-deduction');
        });

        // Deduction Type
        Route::prefix('deductiontype')->group(function () {
            Route::get('', [DeductionTypeController::class, 'index'])->name('deductiontype')->middleware('can:read-deduction-type');
            Route::get('getdata', [DeductionTypeController::class, 'getData'])->name('deductiontype.getdata');
            Route::post('store', [DeductionTypeController::class, 'store'])->name('deductiontype.store');
            Route::get('/edit/{id}', [DeductionTypeController::class, 'show'])->name('deductiontype.edit')->middleware('can:update-deduction-type');
            Route::put('/update/{id}', [DeductionTypeController::class, 'update'])->name('deductiontype.update');
            Route::delete('/delete/{id}', [DeductionTypeController::class, 'destroy'])->name('deductiontype.delete')->middleware('can:delete-deduction-type');
        });

        // Allowance
        Route::prefix('allowance')->group(function () {
            Route::get('', [AllowanceController::class, 'index'])->name('allowance')->middleware('can:read-allowance');
            Route::get('getdata', [AllowanceController::class, 'getData'])->name('allowance.getdata');
            Route::post('store', [AllowanceController::class, 'store'])->name('allowance.store');
            Route::get('/edit/{id}', [AllowanceController::class, 'show'])->name('allowance.edit')->middleware('can:update-allowance');
            Route::put('/update/{id}', [AllowanceController::class, 'update'])->name('allowance.update');
            Route::delete('/delete/{id}', [AllowanceController::class, 'destroy'])->name('allowance.delete')->middleware('can:delete-allowance');
        });

        // Allowance Type
        Route::prefix('allowancetype')->group(function () {
            Route::get('', [AllowanceTypeController::class, 'index'])->name('allowancetype')->middleware('can:read-allowance-type');
            Route::get('getdata', [AllowanceTypeController::class, 'getData'])->name('allowancetype.getdata');
            Route::post('store', [AllowanceTypeController::class, 'store'])->name('allowancetype.store');
            Route::get('/edit/{id}', [AllowanceTypeController::class, 'show'])->name('allowancetype.edit')->middleware('can:update-allowance-type');
            Route::put('/update/{id}', [AllowanceTypeController::class, 'update'])->name('allowancetype.update');
            Route::delete('/delete/{id}', [AllowanceTypeController::class, 'destroy'])->name('allowancetype.delete')->middleware('can:delete-allowance-type');
        });

        // Position
        Route::prefix('position')->group(function () {
            Route::get('', [PositionController::class, 'index'])->name('position')->middleware('can:read-position');
            Route::get('getdata', [PositionController::class, 'getData'])->name('position.getdata');
            Route::post('store', [PositionController::class, 'store'])->name('position.store');
            Route::get('/edit/{id}', [PositionController::class, 'show'])->name('position.edit')->middleware('can:update-position');
            Route::put('/update/{id}', [PositionController::class, 'update'])->name('position.update');
            Route::delete('/delete/{id}', [PositionController::class, 'destroy'])->name('position.delete')->middleware('can:delete-position');
        });

        // Department
        Route::prefix('department')->group(function () {
            Route::get('', [DepartmentController::class, 'index'])->name('department')->middleware('can:read-department');
            Route::get('getdata', [DepartmentController::class, 'getData'])->name('department.getdata');
            Route::post('store', [DepartmentController::class, 'store'])->name('department.store');
            Route::get('/edit/{id}', [DepartmentController::class, 'show'])->name('department.edit')->middleware('can:update-department');
            Route::put('/update/{id}', [DepartmentController::class, 'update'])->name('department.update');
            Route::delete('/delete/{id}', [DepartmentController::class, 'destroy'])->name('department.delete')->middleware('can:delete-department');
        });

        // Employee
        Route::prefix('employee')->group(function () {
            Route::get('', [EmployeeController::class, 'index'])->name('employee')->middleware('can:read-employee');
            Route::get('getdata', [EmployeeController::class, 'getData'])->name('employee.getdata');
            // Route::get('/details/{id}', [EmployeeController::class, 'details'])->name('employee.details')->middleware('can:read-employee');
            Route::get('/add', [EmployeeController::class, 'create'])->name('employee.add')->middleware('can:create-employee');
            Route::post('store', [EmployeeController::class, 'store'])->name('employee.store');
            Route::get('/edit/{id}', [EmployeeController::class, 'show'])->name('employee.edit')->middleware('can:update-employee');
            Route::put('/update/{id}', [EmployeeController::class, 'update'])->name('employee.update');
            Route::delete('/delete/{id}', [EmployeeController::class, 'destroy'])->name('employee.delete')->middleware('can:delete-employee');
        });

        // Salary
        Route::prefix('salary')->group(function () {
            Route::get('', [SalaryController::class, 'index'])->name('salary')->middleware('can:read-salary');
            Route::get('getdata', [SalaryController::class, 'getData'])->name('salary.getdata');
            // Route::get('/details/{id}', [SalaryController::class, 'details'])->name('salary.details')->middleware('can:read-salary');
            Route::get('/add', [SalaryController::class, 'create'])->name('salary.add')->middleware('can:create-salary');
            Route::post('store', [SalaryController::class, 'store'])->name('salary.store');
            Route::get('/details/{id}', [SalaryController::class, 'details'])->name('salary.details')->middleware('can:read-salary');
            Route::get('/edit/{id}', [SalaryController::class, 'show'])->name('salary.edit')->middleware('can:update-salary');
            Route::put('/update/{id}', [SalaryController::class, 'update'])->name('salary.update');
            Route::delete('/delete/{id}', [SalaryController::class, 'destroy'])->name('salary.delete')->middleware('can:delete-salary');
            Route::get('/generate-slip/{id}', [SalaryController::class, 'generateSalarySlip'])->name('salary.generate-slip');
        });
    });

    Route::prefix('submission')->group(function () {
        Route::prefix('leave')->group(function () {
            Route::get('', [LeaveController::class, 'index'])->name('leave')->middleware('can:read-leave');
            Route::get('getdata', [LeaveController::class, 'getData'])->name('leave.getdata');
            Route::post('store', [LeaveController::class, 'store'])->name('leave.store');
            Route::get('/edit/{id}', [LeaveController::class, 'show'])->name('leave.edit')->middleware('can:update-leave');
            Route::put('/update/{id}', [LeaveController::class, 'update'])->name('leave.update');
            Route::delete('/delete/{id}', [LeaveController::class, 'destroy'])->name('leave.delete')->middleware('can:delete-leave');
        });
    });

    Route::prefix('transaction')->group(function () {
        Route::prefix('work-product')->group(function () {
            Route::get('', [WorkProductController::class, 'index'])->name('workproduct')->middleware('can:read-work-product');
            Route::get('getdata', [WorkProductController::class, 'getData'])->name('workproduct.getdata');
            Route::get('/details/{id}', [WorkProductController::class, 'details'])->name('workproduct.details')->middleware('can:read-work-product');
            Route::get('/add', [WorkProductController::class, 'create'])->name('workproduct.add')->middleware('can:create-work-product');
            Route::post('store', [WorkProductController::class, 'store'])->name('workproduct.store');
            Route::get('/edit/{id}', [WorkProductController::class, 'show'])->name('workproduct.edit')->middleware('can:update-work-product');
            Route::put('/update/{id}', [WorkProductController::class, 'update'])->name('workproduct.update');
            Route::delete('/delete/{id}', [WorkProductController::class, 'destroy'])->name('workproduct.delete')->middleware('can:delete-work-product');
        });
    });

    // report
    Route::prefix('report')->group(function () {
        Route::prefix('product-stock')->group(function () {
            Route::get('', [BranchProductStockController::class, 'index'])->name('product-stock');
            Route::get('getdata', [BranchProductStockController::class, 'getData'])->name('product-stock.getdata');
        });

        Route::prefix('transaction-product')->group(function () {
            Route::get('', [TransactionProductController::class, 'index'])->name('report.transaction-product')->middleware('can:read-transaction-product');
            Route::get('getdata', [TransactionProductController::class, 'getData'])->name('report.transaction-product.getdata');
            Route::get('/details/{id}', [TransactionProductController::class, 'details'])->name('report.transaction-product.details')->middleware('can:read-transaction-product');
            Route::post('/export', [TransactionProductController::class, 'exportExcel'])->name('report.transaction-product.export')->middleware('can:export-transaction-product');
            // Route::get('/add', [TransactionProductController::class,'create'])->name('report.transaction-product.add');
            // Route::post('store', [TransactionProductController::class, 'store'])->name('report.transaction-product.store');
            // Route::get('/edit/{id}', [TransactionProductController::class, 'show'])->name('report.transaction-product.edit');
            // Route::put('/update/{id}', [TransactionProductController::class, 'update'])->name('report.transaction-product.update');
            // Route::delete('/delete/{id}', [TransactionProductController::class, 'destroy'])->name('report.transaction-product.delete');
        });

        //leave report
        Route::prefix('leave-report')->group(function () {
            Route::get('', [LeaveReportController::class, 'index'])->name('leavereport')->middleware('can:read-leave-report');
            Route::get('getdata', [LeaveReportController::class, 'getData'])->name('leavereport.getdata');
            Route::get('/edit/{id}', [LeaveReportController::class, 'show'])->name('leavereport.edit')->middleware('can:read-leave-report');
            Route::put('/update/{id}', [LeaveReportController::class, 'update'])->name('leavereport.update');
        });

        //task report
        Route::prefix('task-report')->group(function () {
            Route::get('', [TaskReportController::class, 'index'])->name('taskreport')->middleware('can:read-task-report');
            Route::get('getdata', [TaskReportController::class, 'getData'])->name('taskreport.getdata');
            Route::get('/detail/{id}', [TaskReportController::class, 'details'])->name('taskreport.details')->middleware('can:read-task-report');
        });

        //transfer product
        Route::prefix('transfer-product')->group(function () {
            Route::get('', [TransferProductController::class, 'index'])->name('transfer')->middleware('can:read-transfer-product');
            Route::get('getdata', [TransferProductController::class, 'getData'])->name('transfer.getdata');
            Route::get('/details/{id}', [TransferProductController::class, 'details'])->name('transfer.details')->middleware('can:read-transfer-product');
            Route::get('/add', [TransferProductController::class, 'create'])->name('transfer.add')->middleware('can:create-transfer-product');
            Route::post('store', [TransferProductController::class, 'store'])->name('transfer.store');
            Route::get('/edit/{id}', [TransferProductController::class, 'show'])->name('transfer.edit')->middleware('can:update-transfer-product');
            Route::put('/update/{id}', [TransferProductController::class, 'update'])->name('transfer.update');
            Route::delete('/delete/{id}', [TransferProductController::class, 'destroy'])->name('transfer.delete')->middleware('can:delete-transfer-product');
        });

        //customer
        Route::prefix('customer')->group(function () {
            Route::get('', [CustomerController::class, 'index'])->name('customer');
            Route::get('getdata', [CustomerController::class, 'getData'])->name('customer.getdata');
            Route::get('/add', [CustomerController::class, 'create'])->name('customer.add');
            Route::get('/add/psb', [CustomerController::class, 'createPsb'])->name('customer.add.psb');
            Route::get('/add/repair', [CustomerController::class, 'createRepair'])->name('customer.add.repair');
            Route::post('store', [CustomerController::class, 'store'])->name('customer.store');
            Route::get('/detail/{id}', [CustomerController::class, 'details'])->name('customer.detail');
            Route::get('/edit/{id}', [CustomerController::class, 'show'])->name('customer.edit');
            Route::put('/update/{id}', [CustomerController::class, 'update'])->name('customer.update');
            Route::delete('/delete/{id}', [CustomerController::class, 'destroy'])->name('customer.delete');
            Route::get('getdataodp/{zone_id}', [CustomerController::class, 'getOdpByZone'])->name('customer.getdataodp');
        });
    });

    // //income product
    // Route::prefix('incomeproduct')->group(function () {
    //     Route::get('', [IncomeProductController::class, 'index'])->name('incomeproduct');
    //     Route::get('getdata', [IncomeProductController::class, 'getData'])->name('incomeproduct.getdata');
    //     Route::post('store', [IncomeProductController::class, 'store'])->name('incomeproduct.store');
    //     Route::get('/edit/{id}', [IncomeProductController::class, 'show'])->name('incomeproduct.edit');
    //     Route::put('/update/{id}', [IncomeProductController::class, 'update'])->name('incomeproduct.update');
    //     Route::delete('/delete/{id}', [IncomeProductController::class, 'destroy'])->name('incomeproduct.delete');
    // });

    // //outcome product
    // Route::prefix('outcomeproduct')->group(function () {
    //     Route::get('', [OutcomeProductController::class, 'index'])->name('outcomeproduct');
    //     Route::get('getdata', [OutcomeProductController::class, 'getData'])->name('outcomeproduct.getdata');
    //     Route::post('store', [OutcomeProductController::class, 'store'])->name('outcomeproduct.store');
    //     Route::get('/edit/{id}', [OutcomeProductController::class, 'show'])->name('outcomeproduct.edit');
    //     Route::put('/update/{id}', [OutcomeProductController::class, 'update'])->name('outcomeproduct.update');
    //     Route::delete('/delete/{id}', [OutcomeProductController::class, 'destroy'])->name('outcomeproduct.delete');
    // });

    //settings
    Route::prefix('settings')->group(function () {
        Route::prefix('profile')->group(function () {
            Route::get('/{id}', [ProfileController::class, 'index'])->name('setting.profile');
            Route::put('/update/{id}', [ProfileController::class, 'update'])->name('setting.profile.update');
        });

        Route::prefix('user')->group(function () {
            Route::get('', [UserController::class, 'index'])->name('user')->middleware('can:read-user');
            Route::get('getdata', [UserController::class, 'getData'])->name('user.getdata');
            Route::post('store', [UserController::class, 'store'])->name('user.store');
            Route::get('/edit/{id}', [UserController::class, 'show'])->name('user.edit')->middleware('can:update-user');
            Route::put('/update/{id}', [UserController::class, 'update'])->name('user.update');
            Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('user.delete')->middleware('can:delete-user');
        });

        Route::prefix('role')->group(function () {
            Route::get('', [RoleController::class, 'index'])->name('role')->middleware('can:read-role');
            Route::get('getdata', [RoleController::class, 'getData'])->name('role.getdata');
            Route::get('add', [RoleController::class, 'create'])->name('role.add')->middleware('can:create-role');
            Route::post('store', [RoleController::class, 'store'])->name('role.store');
            Route::get('/edit/{id}', [RoleController::class, 'show'])->name('role.edit')->middleware('can:update-role');
            Route::put('/update/{id}', [RoleController::class, 'update'])->name('role.update');
            Route::delete('/delete/{id}', [RoleController::class, 'destroy'])->name('role.delete')->middleware('can:delete-role');
        });

        Route::prefix('general')->group(function () {
            Route::get('', [SettingController::class, 'index'])->name('setting')->middleware('can:read-setting');
            Route::post('/update', [SettingController::class, 'update'])->name('setting.update');
        });
    });
});
