<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $developer = Role::create(['name' => 'Developer']);
        // $teknisi = Role::create(['name' => 'Teknisi']);
        // $admin = Role::create(['name' => 'Administrator']);
        // $odp = Role::create(['name' => 'ODP']);
        // $listrik = Role::create(['name' => 'Listrik']);
        $employee = Role::create(['name' => 'Employee']);

        $allpermission = Permission::all();
        
        $developer->givePermissionTo([
            $allpermission
        ]);

        // $admin->givePermissionTo([
        //     'read-dashboard',
        //     'read-branch',
        //     'create-branch',
        //     'update-branch',
        //     'delete-branch',
        //     'read-odp',
        //     'create-odp',
        //     'update-odp',
        //     'delete-odp',
        //     'read-product',
        //     'create-product',
        //     'update-product',
        //     'delete-product',
        //     'read-unit-product',
        //     'create-unit-product',
        //     'update-unit-product',
        //     'delete-unit-product',
        //     'read-zone',
        //     'delete-zone',
        //     'read-zone-odp',
        //     'delete-zone-odp',
        //     'read-product-role',
        //     'create-product-role',
        //     'update-product-role',
        //     'delete-product-role',
        //     // Transaction
        //     'read-transfer-product',
        //     'create-transfer-product',
        //     'update-transfer-product',
        //     'delete-transfer-product',
        //     'read-work-product',
        //     'create-work-product',
        //     'update-work-product',
        //     'delete-work-product',
        //     'read-transaction-product',
        //     'export-transaction-product',
        //     // Setting
        //     'read-user',
        //     'create-user',
        //     'update-user',
        //     'delete-user',
        //     'read-role',
        //     'create-role',
        //     'update-role',
        //     'delete-role',
        //     'read-setting',
        // ]);

        // $teknisi->givePermissionTo([
        //     'read-dashboard',
        //     'read-transfer-product',
        // ]);

        // $odp->givePermissionTo([
        //     'read-dashboard',
        //     'read-transfer-product',
        // ]);

        // $listrik->givePermissionTo([
        //     'read-dashboard',
        // ]);

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
