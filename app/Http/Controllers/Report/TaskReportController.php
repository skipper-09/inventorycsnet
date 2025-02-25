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
            'employeeTask.taskAssign', // Relasi ke TaskAssign melalui EmployeeTask
            'employeeTask.taskDetail.task',  // Relasi ke Task melalui TaskDetail
        ])->get();

        // Format data untuk tampilan
        $formattedReports = $taskReports->map(function ($report) {
            $employeeTask = $report->employeeTask;
            $taskAssign = $employeeTask->taskAssign;  // Mengambil TaskAssign melalui EmployeeTask

            // Menyusun data employee
            $employeeData = $employeeTask->employee ? [
                'id' => $employeeTask->employee->id,
                'name' => $employeeTask->employee->name ?? 'Unknown',
            ] : null;

            // Menyusun data task melalui taskDetail
            $taskDetail = $employeeTask->taskDetail;
            $task = $taskDetail ? $taskDetail->task : null;

            // Mengambil status dengan method getStatusBadge()
            $statusBadge = $employeeTask->getStatusBadge($employeeTask->status);

            return [
                'id' => $report->id,
                'report_type' => $report->report_type,
                'report_content' => $report->report_content,
                'report_image' => $report->report_image,
                'taskAssign' => $taskAssign,  // Menyimpan data taskAssign
                'employee' => $employeeData,  // Menyimpan data employee
                'task' => $task,  // Menyimpan data task
                'statusBadge' => $statusBadge,  // Menyimpan status badge
            ];
        });

        $data = [
            'title' => 'Task Report',
            'taskReports' => $formattedReports
        ];

        return view('pages.report.task.index', $data);
    }
}