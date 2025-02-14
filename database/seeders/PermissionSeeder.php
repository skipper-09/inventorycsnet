<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arrayOfPermissionNames = [
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
            'read-deduction',
            'create-deduction',
            'update-deduction',
            'delete-deduction',
            'read-deduction-type',
            'create-deduction-type',
            'update-deduction-type',
            'delete-deduction-type',
            'read-allowance',
            'create-allowance',
            'update-allowance',
            'delete-allowance',
            'read-allowance-type',
            'create-allowance-type',
            'update-allowance-type',
            'delete-allowance-type',
            'read-position',
            'create-position',
            'update-position',
            'delete-position',
            'read-department',
            'create-department',
            'update-department',
            'delete-department',
            //tasktemplate
            'read-task-template',
            'create-task-template',
            'update-task-template',
            'delete-task-template',
            //leave report
            'read-leave-report',
            'create-leave-report',
            'update-leave-report',
            'delete-leave-report',
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
            
        ];

        $permissions = collect($arrayOfPermissionNames)->map(function ($permission) {
            return ['name' => $permission, 'guard_name' => 'web'];
        });

        Permission::insert($permissions->toArray());
    }
}
