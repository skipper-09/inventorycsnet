<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\TaskReport;

class TaskReportController extends Controller
{
    public function index()
    {
        // Mengambil data task reports dengan relasi yang benar
        $taskReports = TaskReport::with([
            'employeeTask.employee',  // Relasi ke Employee melalui EmployeeTask
            'employeeTask.taskDetail.task',  // Relasi ke Task melalui TaskDetail
        ])->get();

        // Format data untuk tampilan
        $formattedReports = $taskReports->map(function ($report) {
            // Mengambil data terkait dengan relasi employeeTask
            $employeeTask = $report->employeeTask;

            // Menyusun data employee
            $employeeData = $employeeTask->employee ? [
                'id' => $employeeTask->employee->id,
                'name' => $employeeTask->employee->name ?? 'Unknown',
            ] : null;

            // Menyusun data task melalui taskDetail
            $taskDetail = $employeeTask->taskDetail;
            $task = $taskDetail ? $taskDetail->task : null;

            return [
                'id' => $report->id,
                'report_type' => $report->report_type,
                'report_content' => $report->report_content,
                'report_image' => $report->report_image,
                'assignment' => $employeeTask,  // Langsung masukkan objek EmployeeTask
                'employee' => $employeeData,  // Menyimpan data employee
                'task' => $task,  // Menyimpan data task
            ];
        });

        // Data untuk dikirim ke tampilan
        $data = [
            'title' => 'Task Report',
            'taskReports' => $formattedReports
        ];

        // Mengembalikan tampilan dengan data
        return view('pages.report.task.index', $data);
    }
}
