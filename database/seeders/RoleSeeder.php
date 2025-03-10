<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $developer = Role::create(['name' => 'Developer']);
        $teknisi = Role::create(['name' => 'Teknisi']);
        $admin = Role::create(['name' => 'Administrator']);
        $odp = Role::create(['name' => 'ODP']);
        $listrik = Role::create(['name' => 'Listrik']);
        $employee = Role::create(['name' => 'Employee']);

        $developer->givePermissionTo([
            // Dashboard
            'read-dashboard',
            // Branch
            'read-branch',
            'create-branch',
            'update-branch',
            'delete-branch',
            // ODP
            'read-odp',
            'create-odp',
            'update-odp',
            'delete-odp',
            // Product
            'read-product',
            'create-product',
            'update-product',
            'delete-product',
            // Unit Product
            'read-unit-product',
            'create-unit-product',
            'update-unit-product',
            'delete-unit-product',
            // Zone
            'read-zone',
            'delete-zone',
            'read-zone-odp',
            'delete-zone-odp',
            // Product Role
            'read-product-role',
            'create-product-role',
            'update-product-role',
            'delete-product-role',
            // Deduction
            'read-deduction',
            'create-deduction',
            'update-deduction',
            'delete-deduction',
            // Deduction Type
            'read-deduction-type',
            'create-deduction-type',
            'update-deduction-type',
            'delete-deduction-type',
            // Allowance
            'read-allowance',
            'create-allowance',
            'update-allowance',
            'delete-allowance',
            // Allowance Type
            'read-allowance-type',
            'create-allowance-type',
            'update-allowance-type',
            'delete-allowance-type',
            // Position
            'read-position',
            'create-position',
            'update-position',
            'delete-position',
            //leave
            // 'read-leave',
            // 'create-leave',
            // 'update-leave',
            // 'delete-leave',
            //leave report
            'read-leave-report',
            'create-leave-report',
            'update-leave-report',
            'delete-leave-report',
            //task report
            'read-task-report',
            'create-task-report',
            'update-task-report',
            'delete-task-report',
            //activity report
            'read-activity-report',
            'create-activity-report',
            'update-activity-report',
            'delete-activity-report',
            'export-activity-report',
            //customer
            'read-customer',
            'create-customer',
            'update-customer',
            'delete-customer',
            //departement
            'read-department',
            'create-department',
            'update-department',
            'delete-department',
            //task template
            'read-task-template',
            'create-task-template',
            'update-task-template',
            'delete-task-template',
            //task
            'read-task',
            'create-task',
            'update-task',
            'delete-task',
            //detailtask
            'read-detail-task',
            'create-detail-task',
            'update-detail-task',
            'delete-detail-task',
            //assigment
            'read-assignment',
            'create-assignment',
            'update-assignment',
            'delete-assignment',
            //assigment Data
            'read-assigmentdata',
            'create-assigmentdata',
            'update-assigmentdata',
            'delete-assigmentdata',
            // employee
            'read-employee',
            'create-employee',
            'update-employee',
            'delete-employee',
            // salary
            'read-salary',
            'create-salary',
            'update-salary',
            'delete-salary',
            'export-salary',
            // Transfer Product
            'read-transfer-product',
            'create-transfer-product',
            'update-transfer-product',
            'delete-transfer-product',
            // Work Product
            'read-work-product',
            'create-work-product',
            'update-work-product',
            'delete-work-product',
            // Transaction Product
            'read-transaction-product',
            'export-transaction-product',
            // User
            'read-user',
            'create-user',
            'update-user',
            'delete-user',
            // Role
            'read-role',
            'create-role',
            'update-role',
            'delete-role',
            //activity-log
            'read-activity-log',
            'delete-activity-log',
            'read-setting',
        ]);

        $admin->givePermissionTo([
            'read-dashboard',
            'read-branch',
            'create-branch',
            'update-branch',
            'delete-branch',
            'read-odp',
            'create-odp',
            'update-odp',
            'delete-odp',
            'read-product',
            'create-product',
            'update-product',
            'delete-product',
            'read-unit-product',
            'create-unit-product',
            'update-unit-product',
            'delete-unit-product',
            'read-zone',
            'delete-zone',
            'read-zone-odp',
            'delete-zone-odp',
            'read-product-role',
            'create-product-role',
            'update-product-role',
            'delete-product-role',
            // Transaction
            'read-transfer-product',
            'create-transfer-product',
            'update-transfer-product',
            'delete-transfer-product',
            'read-work-product',
            'create-work-product',
            'update-work-product',
            'delete-work-product',
            'read-transaction-product',
            'export-transaction-product',
            // Setting
            'read-user',
            'create-user',
            'update-user',
            'delete-user',
            'read-role',
            'create-role',
            'update-role',
            'delete-role',
            'read-setting',
        ]);

        $teknisi->givePermissionTo([
            'read-dashboard',
            'read-transfer-product',
        ]);

        $odp->givePermissionTo([
            'read-dashboard',
            'read-transfer-product',
        ]);

        $listrik->givePermissionTo([
            'read-dashboard',
        ]);

        $employee->givePermissionTo([
            'read-dashboard',
            //salary
            'read-salary',
            //leave report
            'read-leave-report',
            'create-leave-report',
            'update-leave-report',
            //assigment Data
            'read-assigmentdata',
            'create-assigmentdata',
            'update-assigmentdata',
            'delete-assigmentdata', 
            //activity report
            'read-activity-report',
            'create-activity-report',
            'update-activity-report',
            'delete-activity-report',
        ]);
    }
}
