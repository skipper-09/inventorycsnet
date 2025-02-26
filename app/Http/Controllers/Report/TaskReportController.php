<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\EmployeeTask;
use App\Models\TaskReport;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class TaskReportController extends Controller
{
    // public function index()
    // {
    //     // Mengambil data task reports dengan relasi yang benar
    //     $taskReports = TaskReport::with([
    //         'employeeTask.employee',  // Relasi ke Employee melalui EmployeeTask
    //         'employeeTask.taskAssign', // Relasi ke TaskAssign melalui EmployeeTask
    //         'employeeTask.taskDetail.task',  // Relasi ke Task melalui TaskDetail
    //     ])->get();

    //     // Format data untuk tampilan
    //     $formattedReports = $taskReports->map(function ($report) {
    //         $employeeTask = $report->employeeTask;
    //         $taskAssign = $employeeTask->taskAssign;  // Mengambil TaskAssign melalui EmployeeTask

    //         // Menyusun data employee
    //         $employeeData = $employeeTask->employee ? [
    //             'id' => $employeeTask->employee->id,
    //             'name' => $employeeTask->employee->name ?? 'Unknown',
    //         ] : null;

    //         // Menyusun data task melalui taskDetail
    //         $taskDetail = $employeeTask->taskDetail;
    //         $task = $taskDetail ? $taskDetail->task : null;

    //         // Mengambil status dengan method getStatusBadge()
    //         $statusBadge = $employeeTask->getStatusBadge($employeeTask->status);

    //         return [
    //             'id' => $report->id,
    //             'report_type' => $report->report_type,
    //             'report_content' => $report->report_content,
    //             'report_image' => $report->report_image,
    //             'taskAssign' => $taskAssign,  // Menyimpan data taskAssign
    //             'employee' => $employeeData,  // Menyimpan data employee
    //             'task' => $task,  // Menyimpan data task
    //             'statusBadge' => $statusBadge,  // Menyimpan status badge
    //         ];
    //     });

    //     $data = [
    //         'title' => 'Task Report',
    //         'taskReports' => $formattedReports
    //     ];

    //     return view('pages.report.task.index', $data);
    // }

    public function index()
    {
        $data = [
            'title' => 'Task Report',
        ];

        return view('pages.report.task.index', $data);
    }

    public function getData()
    {
        $data = TaskReport::with([
            'employeeTask.employee',
            'employeeTask.taskAssign',
            'employeeTask.taskDetail.task',
        ])->orderByDesc('id')->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                $userauth = User::with('roles')->where('id', Auth::id())->first();
                $button = '';

                if ($userauth->can('read-task-report')) {
                    $button .= '<a href="' . route('taskreport.details', ['id' => $data->id]) . '"
                        class="btn btn-sm btn-info" 
                        data-id="' . $data->id . '"
                        data-type="details"
                        data-toggle="tooltip"
                        data-placement="bottom"
                        title="Details">
                        <i class="fas fa-eye"></i>
                    </a>';
                }
                return '<div class="d-flex gap-2">' . $button . '</div>';
            })
            ->addColumn('assignment_date', function ($data) {
                return formatDate($data->employeeTask->taskAssign->assignment_date ?? null);
            })
            ->addColumn('employee_name', function ($data) {
                return $data->employeeTask->employee->name ?? 'N/A';
            })
            ->addColumn('location', function ($data) {
                return $data->employeeTask->taskAssign->place ?? 'N/A';
            })
            ->rawColumns(['action', 'employee_name', 'assignment_date', 'location'])
            ->make(true);
    }

    public function details($id)
    {
        $employeeTask = EmployeeTask::with([
            'employee',
            'taskAssign',
            'taskDetail.task',
            'taskReports.reportImage', // Load all report images
        ])->findOrFail($id);

        // Mengambil nama tugas melalui taskDetail yang memiliki relasi task
        $taskName = $employeeTask->taskDetail->task->name ?? 'N/A';
        $taskAssign = $employeeTask->taskAssign;
        $taskReports = $employeeTask->taskReports;

        // Group the images by report_type for each report
        foreach ($taskReports as $report) {
            $report->beforeImages = $report->reportImage->where('report_type', 'before');
            $report->afterImages = $report->reportImage->where('report_type', 'after');
        }

        // Prepare the data
        $data = [
            "title" => "Task Report Details",
            "employeeTask" => $employeeTask,
            "taskName" => $taskName,
            "taskAssign" => $taskAssign,
            "taskReports" => $taskReports,
        ];

        return view('pages.report.task.details', $data);
    }
}