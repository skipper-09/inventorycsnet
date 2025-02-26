<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Menggunakan insert untuk memasukkan beberapa task sekaligus
        Task::insert([
            [
                'name' => 'Task 1',
                'description' => 'Deskripsi untuk task pertama',
                'created_at' => now(), // Pastikan untuk menyertakan created_at dan updated_at jika menggunakan timestamps
                'updated_at' => now(),
            ],
            [
                'name' => 'Task 2',
                'description' => 'Deskripsi untuk task kedua',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Task 3',
                'description' => 'Deskripsi untuk task ketiga',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
