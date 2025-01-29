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
            // Transaction
            'read-transfer-product',
            'create-transfer-product',
            'update-transfer-product',
            'delete-transfer-product',
            'read-work-product',
            'create-work-product',
            'update-work-product',
            'delete-work-product',
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

        $admin->givePermissionTo([
            'read-dashboard',
            'read-transfer-product',
            'read-work-product',
        ]);

        $teknisi->givePermissionTo([
            'read-dashboard',
        ]);

        $odp->givePermissionTo([
            'read-dashboard',
        ]);

        $listrik->givePermissionTo([
            'read-dashboard',
        ]);
    }
}
