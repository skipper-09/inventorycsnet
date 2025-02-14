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

        $developer->givePermissionTo([
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
            'read-task-template',
            'create-task-template',
            'update-task-template',
            'delete-task-template',
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
    }
}
