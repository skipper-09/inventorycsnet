<?php

namespace Database\Seeders;

use App\Models\TaskTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TaskTemplate::create([
            'name'=>'Testing',
            'description'=>'Testing',
            'slug'=>'Testing',
        ]);
    }
}
