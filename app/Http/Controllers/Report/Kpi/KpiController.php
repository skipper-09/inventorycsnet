<?php

namespace App\Http\Controllers\Report\Kpi;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\TaskAssign;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class KpiController extends Controller
{
    public function index()
    {

        $data = [
            'title' => 'Kpi Karyawan',
        ];

        return view('pages.report.kpi.index', $data);
    }




    //getdata
    public function getData(Request $request)
    {
        $bulan = $request->bulan ?: Carbon::now()->format('m');
        $tahun = $request->tahun ?: Carbon::now()->format('Y');

        $bulanTahun = Carbon::createFromFormat('Y-m', $tahun . '-' . $bulan);

        $data = [];

        $tasksByEmployee = TaskAssign::whereMonth('assign_date', $bulan)
            ->whereYear('assign_date', $tahun)->orderByDesc('created_at')
            ->get()
            ->groupBy('assignee_id');

        foreach ($tasksByEmployee as $employeeId => $tasks) {
            $completedTasks = 0;
            $inreview = 0;
            $overdueTasks = 0;

            foreach ($tasks as $taskAssign) {
                foreach ($taskAssign->employeeTasks as $employeeTask) {
                    if ($employeeTask->status == 'complated') {
                        $completedTasks++;
                    } elseif ($employeeTask->status == 'in_review') {
                        $inreview++;
                    } else {
                        $overdueTasks++;
                    }
                }
            }

            $totalTasks = $completedTasks + $inreview + $overdueTasks;

            if ($totalTasks > 0) {
                $kpi = ($completedTasks / $totalTasks) * 100;
            } else {
                $kpi = 0;
            }

            $employee = Employee::find($employeeId);
            $employeeName = $employee ? $employee->name : 'N/A';

            $formattedBulanTahun = $bulanTahun->format('F Y');

            $data[] = [
                'assigne_id' => $taskAssign->assignee_id,
                'employee_name' => $employeeName,
                'completed_tasks' => $completedTasks,
                'inreview_tasks' => $inreview,
                'overdue_tasks' => $overdueTasks,
                'kpi' => number_format($kpi, 2) . '%',
                'bulan' => $formattedBulanTahun,
            ];
        }

        // Kembalikan DataTables response
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($data) use ($bulan, $tahun) {
                $userauth = User::with('roles')->where('id', Auth::id())->first();
                $button = '';
                if ($userauth->can('update-unit-product')) {
                    $button .= ' <a href="' . route('kpi.employee.detail', ['assigne_id' => $data['assigne_id'],'bulan'=>$bulan,'tahun'=>$tahun]) . '" class="btn btn-sm btn-info action mr-1" data-id=' . $data['assigne_id'] . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Detail Data"><i class="fas fa-eye"></i></a>';
                }

                return '<div class="d-flex gap-2">' . $button . '</div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    public function detail($assigne_id, $bulan, $tahun)
    {
        $bulanTahun = Carbon::createFromFormat('Y-m', $tahun . '-' . $bulan);

        $taskAssigns = TaskAssign::with(['employeeTasks', 'employeeTasks.taskDetail', 'employeeTasks.taskReports', 'assignee', 'employeeTasks.taskDetail.task'])
            ->where('assignee_id', $assigne_id)
            ->whereMonth('assign_date', $bulan)
            ->whereYear('assign_date', $tahun)
            ->get();
        
        if ($taskAssigns->isEmpty()) {
            return redirect()->route('employee.task.list')->with('error', 'Data tidak ditemukan');
        }
    
        $tasks = [];
        foreach ($taskAssigns as $taskAssign) {
            $taskData = [];
    
            $groupedTasks = $taskAssign->employeeTasks->groupBy(function($task) {
                return $task->taskDetail->task->name;
            });
    
            foreach ($groupedTasks as $taskName => $employeeTasks) {
                $taskReports = [];
    
                foreach ($employeeTasks as $employeeTask) {
                    $reports = $employeeTask->taskReports;
    
                    $reportData = [];
                    foreach ($reports as $report) {
                        $reportData[] = [
                            'report_content' => $report->report_content,
                            'reason_not_complated' => $report->reason_not_complated,
                            'report_images' => $report->reportImage->pluck('image'),
                        ];
                    }
    
                    $taskReports[] = [
                        'task_name' => $employeeTask->taskDetail->name, 
                        'status' => $employeeTask->getStatus(),              
                        'reports' => $reportData,
                    ];
                }
    
                $taskData[] = [
                    'task_name' => $taskName,
                    'tasks' => $taskReports,
                ];
            }
    
            $tasks[] = [
                'task_assign_id' => $taskAssign->id,
                'task_assign_date' => Carbon::parse($taskAssign->assign_date)->format('F Y'),
                'employee_name' => $taskAssign->assignee->name,
                'position' => $taskAssign->assignee,
                'place'=> $taskAssign->place,
                'template'=> $taskAssign->tasktemplate->name,
                'tasks' => $taskData,
            ];
        }
        
        // Mengembalikan data ke view
        return view('pages.report.kpi.detail', [
            'title'=>'Detail ' . $taskAssign->assignee->name,
            'tasks' => $tasks,
            'bulanTahun' => $bulanTahun->format('F Y'),
        ]);
    }

}
