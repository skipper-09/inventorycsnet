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
use App\Models\ReportImage;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Storage;

class TaskReportSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID');

        // Buat employee tasks jika belum ada
        if (EmployeeTask::count() == 0) {
            // Verifikasi data yang dibutuhkan tersedia
            $taskDetails = TaskDetail::all();
            if ($taskDetails->isEmpty()) {
                $this->command->info('Task Detail data tidak tersedia. Silakan jalankan TaskDetailSeeder terlebih dahulu.');
                return;
            }

            $employees = Employee::all();
            if ($employees->isEmpty()) {
                $this->command->info('Employee data tidak tersedia. Silakan jalankan EmployeeSeeder terlebih dahulu.');
                return;
            }

            $taskAssigns = TaskAssign::all();
            if ($taskAssigns->isEmpty()) {
                $this->command->info('Task Assign data tidak tersedia. Silakan jalankan TaskAssignSeeder terlebih dahulu.');
                return;
            }

            foreach ($taskDetails as $detail) {
                // Pastikan tidak melebihi jumlah employees yang tersedia
                $assigneeCount = min(rand(1, 3), $employees->count());
                $assignedEmployees = $employees->random($assigneeCount);

                foreach ($assignedEmployees as $employee) {
                    EmployeeTask::create([
                        'task_assign_id' => $taskAssigns->random()->id,
                        'task_detail_id' => $detail->id,
                        'employee_id' => $employee->id,
                        'status' => $faker->randomElement(['pending', 'overdue', 'complated']), // Perbaiki typo 'complated' menjadi 'completed'
                        'created_at' => $faker->dateTimeBetween('-1 month', 'now'),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Buat task reports
        $employeeTasks = EmployeeTask::all();
        
        if ($employeeTasks->isEmpty()) {
            $this->command->info('EmployeeTask data tidak tersedia. Tidak dapat membuat TaskReport.');
            return;
        }

        foreach ($employeeTasks as $employeeTask) {
            // Generate "before" reports untuk semua task
            if (rand(1, 10) <= 8) {
                $taskReport = TaskReport::create([
                    'employee_task_id' => $employeeTask->id,
                    'report_content' => $faker->paragraph(rand(1, 3)),
                    'reason_not_complated' => $employeeTask->status !== 'complated' ? 
                        $faker->randomElement(['Tertunda karena masalah teknis', 'Menunggu persetujuan', 'Sedang dalam proses']) : null, // Perbaiki typo field 'reason_not_complated'
                    'created_at' => $faker->dateTimeBetween($employeeTask->created_at, 'now'),
                    'updated_at' => now(),
                ]);

                // Generate report image for "before"
                $imageName = 'reports/before_' . uniqid() . '.jpg';
                
                ReportImage::create([
                    'report_task_id' => $taskReport->id,
                    'report_type' => 'before',
                    'image' => $imageName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Generate "after" reports for completed tasks
            if ($employeeTask->status === 'completed' && rand(1, 10) <= 9) {
                $taskReport = TaskReport::create([
                    'employee_task_id' => $employeeTask->id,
                    'report_content' => $faker->paragraph(rand(1, 4)),
                    'reason_not_complated' => null, // Tidak perlu alasan untuk tugas yang selesai
                    'created_at' => $faker->dateTimeBetween($employeeTask->created_at, 'now'),
                    'updated_at' => now(),
                ]);

                // Generate report image for "after"
                $imageName = 'reports/after_' . uniqid() . '.jpg';
                
                ReportImage::create([
                    'report_task_id' => $taskReport->id,
                    'report_type' => 'after',
                    'image' => $imageName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        
        $this->command->info('Task Reports seeded successfully!');
    }
}