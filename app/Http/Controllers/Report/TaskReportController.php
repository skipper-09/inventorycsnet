<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\EmployeeTask;
use App\Models\Task;
use App\Models\TaskAssign;
use App\Models\TaskReport;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TaskReportController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Task Report',
        ];

        return view('pages.report.task.index', $data);
    }

    public function getData(Request $request)
    {
        // Mulai query utama untuk TaskReport, dengan memuat relasi yang diperlukan
        $query = EmployeeTask::with([
            'employee',
            'taskAssign',
            'taskDetail.task', // Pastikan untuk mengambil relasi task
        ])->orderByDesc('id');

        // Apply assign_date filter if provided
        if ($request->filled('assign_date')) {
            $query->whereHas('taskAssign', function ($q) use ($request) {
                $q->whereDate('assign_date', $request->input('assign_date'));
            });
        }

        // Apply status filter if provided
        if ($request->filled('status')) {
            $query->whereHas('employeeTask', function ($q) use ($request) {
                $q->where('status', $request->input('status'));
            });
        }

        // Ambil data dan kelompokkan berdasarkan task_id
        $data = $query->get()->groupBy(function ($item) {
            return $item->task_assign_id;
        });

        // Ambil data pertama dari setiap grup berdasarkan task_id
        $flattenedData = $data->map(function ($group) {
            return $group->first();
        });

        // Kembalikan data yang telah dikelompokkan dan diproses
        return DataTables::of($flattenedData)
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
                return formatDate($data->taskAssign->assignment_date ?? null);
            })
            ->addColumn('employee_name', function ($data) {
                return $data->employee->name ?? 'N/A';
            })
            ->addColumn('tugas', function ($data) {
                return $data->taskAssign->tasktemplate->name ?? 'N/A';
            })
            ->addColumn('location', function ($data) {
                return $data->taskAssign->place ?? 'N/A';
            })
            ->rawColumns(['action', 'employee_name', 'assignment_date', 'location','tugas'])
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
        $taskName = $employeeTask->taskAssign->tasktemplate->name ?? 'N/A';
        $taskAssign = $employeeTask->taskAssign;
        $taskReports = $employeeTask->taskReports;

        // Group the images by report_type for each report
        foreach ($taskReports as $report) {
            $report->beforeImages = $report->reportImage->where('report_type', 'before');
            $report->afterImages = $report->reportImage->where('report_type', 'after');
        }

        // Get all tasks with the same task_id and group them by assignment date
        $task_assign_id = $employeeTask->task_assign_id ?? null;

        $relatedTasks = null;
        if ($task_assign_id) {

            $relatedTasks = EmployeeTask::with([
                'employee',
                'taskAssign',
                'taskDetail.task',
                'taskReports'
            ])->where('task_assign_id',$task_assign_id)
                ->get()
                ->groupBy(function ($item) {
                    // Group by assignment date
                    return $item->task_assign_id;
                });
            // $relatedTasks = Task::with(['templateTas','templateTas.tasktemplate.taskAssign']) ->whereHas('templateTas', function ($query) use ($template_id) {
            //             $query->where('task_template_id', $template_id);
            //         })->get();
        }

        // dd($relatedTasks);

        $totalReports = $taskReports->count();
        $completedReports = $taskReports->where('reason_not_complated', null)->count();
        $progressPercentage = $totalReports > 0 ? round(($completedReports / $totalReports) * 100) : 0;

        // If there are no reports but the task exists, set progress to 0%
        if ($totalReports === 0) {
            $progressPercentage = 0;
        }

        // Prepare the data
        $data = [
            "title" => "Task Report Details",
            "employeeTask" => $employeeTask,
            "taskName" => $taskName,
            "taskAssign" => $taskAssign,
            "taskReports" => $taskReports,
            "relatedTasks" => $relatedTasks,
            "progressPercentage" => $progressPercentage
        ];

        return view('pages.report.task.details', $data);
    }
}