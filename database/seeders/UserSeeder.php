<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Developer',
            'username' => 'developer',
            'email' => 'csnetdev@gmail.com',
            'password' => Hash::make('csnetdev'),
        ])->assignRole('Developer');

        // User::create([
        //     'name' => 'Teknisi',
        //     'username' => 'teknisi',
        //     'email' => 'teknisi@gmail.com',
        //     'password' => Hash::make('teknisi'),
        // ])->assignRole('Teknisi');

        // User::create([
        //     'name' => 'ODP',
        //     'username' => 'odp123',
        //     'email' => 'odp@gmail.com',
        //     'password' => Hash::make('odp123'),
        // ])->assignRole('ODP');

        // User::create([
        //     'name' => 'Admin',
        //     'username' => 'admin',
        //     'email' => 'admin@gmail.com',
        //     'password' => Hash::make('admin'),
        // ])->assignRole('Administrator');
    }
}
