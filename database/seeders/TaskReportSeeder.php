<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TaskReport;
use App\Models\EmployeeTask;
use App\Models\Employee;
use App\Models\TaskDetail;
use App\Models\Task;
use App\Models\TaskAssign;
use App\Models\TaskTemplate;
use Faker\Factory as Faker;

class TaskReportSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Buat task templates jika belum ada
        if (TaskTemplate::count() == 0) {
            for ($i = 0; $i < 3; $i++) {
                TaskTemplate::create([
                    'name' => 'Template ' . $i,
                    'description' => 'Template description ' . $i,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Buat tasks jika belum ada
        if (Task::count() == 0) {
            for ($i = 0; $i < 5; $i++) {
                Task::create([
                    'name' => $faker->sentence(3),
                    'description' => $faker->paragraph(),
                    'created_at' => $faker->dateTimeBetween('-1 month', 'now'),
                    'updated_at' => now(),
                ]);
            }
        }

        // Buat task details jika belum ada
        if (TaskDetail::count() == 0) {
            $tasks = Task::all();
            foreach ($tasks as $task) {
                $detailCount = rand(2, 5);
                for ($i = 0; $i < $detailCount; $i++) {
                    TaskDetail::create([
                        'task_id' => $task->id,
                        'name' => $faker->sentence(4),
                        'description' => $faker->paragraph(),
                        'created_at' => $faker->dateTimeBetween('-1 month', 'now'),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Buat task assignments jika belum ada
        if (TaskAssign::count() == 0) {
            $employees = Employee::all();
            $tasks = Task::all();
            $taskTemplates = TaskTemplate::all();

            foreach ($tasks as $task) {
                $assigneeCount = rand(1, 3);
                $assignedEmployees = $employees->random($assigneeCount);

                foreach ($assignedEmployees as $employee) {
                    $taskTemplate = $taskTemplates->random();

                    TaskAssign::create([
                        'task_template_id' => $taskTemplate->id,
                        'assignee_id' => $employee->id,
                        'assignee_type' => 'App\Models\Employee',
                        'assign_date' => $faker->dateTimeBetween('-1 month', 'now'),
                        'place' => $faker->city,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Buat employee tasks jika belum ada
        if (EmployeeTask::count() == 0) {
            $taskDetails = TaskDetail::all();
            $employees = Employee::all();
            $taskAssigns = TaskAssign::all();

            foreach ($taskDetails as $detail) {
                $assigneeCount = rand(1, 3);
                $assignedEmployees = $employees->random($assigneeCount);

                foreach ($assignedEmployees as $employee) {
                    EmployeeTask::create([
                        'task_assign_id' => $taskAssigns->random()->id,
                        'task_detail_id' => $detail->id,
                        'employee_id' => $employee->id,
                        'status' => $faker->randomElement(['pending', 'overdue', 'complated']),
                        'created_at' => $faker->dateTimeBetween('-1 month', 'now'),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Buat task reports
        $employeeTasks = EmployeeTask::all();

        foreach ($employeeTasks as $employeeTask) {
            // Generate "before" reports for non-pending tasks
            if ($employeeTask->status != 'pending' && rand(1, 10) <= 8) {
                TaskReport::create([
                    'employee_task_id' => $employeeTask->id,
                    'report_type' => 'before',
                    'report_image' => 'reports/before_' . uniqid() . '.jpg',
                    'report_content' => $faker->paragraph(rand(1, 3)),
                    'created_at' => $faker->dateTimeBetween($employeeTask->created_at, 'now'),
                    'updated_at' => now(),
                ]);
            }

            // Generate "after" reports for completed tasks
            if ($employeeTask->status === 'complated' && rand(1, 10) <= 9) {
                TaskReport::create([
                    'employee_task_id' => $employeeTask->id,
                    'report_type' => 'after',
                    'report_image' => 'reports/after_' . uniqid() . '.jpg',
                    'report_content' => $faker->paragraph(rand(1, 4)),
                    'created_at' => $faker->dateTimeBetween($employeeTask->created_at, 'now'),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}