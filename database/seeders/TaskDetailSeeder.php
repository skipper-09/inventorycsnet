<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\TaskDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tasks = Task::all();

        // Untuk setiap task, kita buat detail task
        foreach ($tasks as $task) {
            // Menggunakan relasi hasMany untuk membuat task detail
            $task->detailtask()->create([
                'name' => 'Detail for ' . $task->name . ' 1',
                'description' => 'Deskripsi detail untuk ' . $task->name . ' pertama',
            ]);

            $task->detailtask()->create([
                'name' => 'Detail for ' . $task->name . ' 2',
                'description' => 'Deskripsi detail untuk ' . $task->name . ' kedua',
            ]);
        }
    }
}
