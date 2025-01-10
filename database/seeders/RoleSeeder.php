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
        ]);

        $admin->givePermissionTo([
            'read-dashboard',
        ]);
    }
}
