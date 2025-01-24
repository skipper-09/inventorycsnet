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
            'read-user',
            'create-user',
            'update-user',
            'delete-user',
            'read-role',
            'create-role',
            'update-role',
            'delete-role',
        ]);

        $admin->givePermissionTo([
            'read-dashboard',
        ]);

        $teknisi->givePermissionTo([
            'read-dashboard',
        ]);
    }
}
