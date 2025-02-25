<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TaskReport;
use App\Models\EmployeeTask;
use App\Models\Employee;
use App\Models\TaskDetail;
use App\Models\Task;
use App\Models\TaskAssign;
use Faker\Factory as Faker;

class TaskReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Create tasks if none exist
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

        // Create task details if none exist
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

        // Create employee tasks if none exist
        if (EmployeeTask::count() == 0) {
            $taskDetails = TaskDetail::all();
            $employees = Employee::all();

            foreach ($taskDetails as $detail) {
                // Assign to 1-3 random employees
                $assigneeCount = rand(1, 3);
                $assignedEmployees = $employees->random($assigneeCount);

                foreach ($assignedEmployees as $employee) {
                    EmployeeTask::create([
                        'task_detail_id' => $detail->id,
                        'employee_id' => $employee->id,
                        'status' => 1,
                        'created_at' => $faker->dateTimeBetween('-1 month', 'now'),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Create task assignments
        if (TaskAssign::count() == 0) {
            $employees = Employee::all();
            $tasks = Task::all();

            foreach ($tasks as $task) {
                // Assign tasks to employees randomly
                $assigneeCount = rand(1, 3);
                $assignedEmployees = $employees->random($assigneeCount);

                foreach ($assignedEmployees as $employee) {
                    TaskAssign::create([
                        'task_template_id' => $task->id,
                        'assignee_id' => $employee->id,
                        'assignee_type' => 'Employee', // Or you can use a dynamic value
                        'assign_date' => $faker->dateTimeBetween('-1 month', 'now'),
                        'place' => $faker->city, // Fake place
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Now create task reports
        $employeeTasks = EmployeeTask::all();

        // Generate reports - before/after images for tasks
        foreach ($employeeTasks as $employeeTask) {
            // For non-pending tasks, create "before" reports
            if (rand(1, 10) <= 8) { // 80% chance
                // Generate fake image path - in real scenario, these would be actual images
                $imagePath = 'reports/before_' . uniqid() . '.jpg';

                // Create a "before" report
                TaskReport::create([
                    'employee_task_id' => $employeeTask->id,
                    'report_type' => 'before',
                    'report_image' => $imagePath,
                    'report_content' => $faker->paragraph(rand(1, 3)),
                    'created_at' => $faker->dateTimeBetween($employeeTask->created_at, 'now'),
                    'updated_at' => now(),
                ]);
            }

            // For completed tasks, add "after" reports
            if ($employeeTask->status === 'complated' && rand(1, 10) <= 9) { // 90% chance for completed tasks
                // Generate fake image path
                $imagePath = 'reports/after_' . uniqid() . '.jpg';

                // Create an "after" report - should be created after the "before" report
                TaskReport::create([
                    'employee_task_id' => $employeeTask->id,
                    'report_type' => 'after',
                    'report_image' => $imagePath,
                    'report_content' => $faker->paragraph(rand(1, 4)),
                    'created_at' => $faker->dateTimeBetween($employeeTask->created_at, 'now'),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('Task reports and assignments seeded successfully!');
    }
}
