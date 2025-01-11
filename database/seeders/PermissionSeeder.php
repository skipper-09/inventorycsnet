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
            'read-zone-odp',
            'create-zone-odp',
            'update-zone-odp',
            'delete-zone-odp',
            'read-user',
            'create-user',
            'update-user',
            'delete-user',
            'read-role',
            'create-role',
            'update-role',
            'delete-role',
        ];
        
        $permissions = collect($arrayOfPermissionNames)->map(function ($permission) {
            return ['name' => $permission, 'guard_name' => 'web'];
        });

        Permission::insert($permissions->toArray());
    }
}
