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
            ->addColumn('action', function ($data) {
                $userauth = User::with('roles')->where('id', Auth::id())->first();
                $button = '';

                // Contoh: Tambahkan tombol untuk aksi edit dan delete
                if ($userauth->can('update-unit-product')) {
                    $button .= ' <button class="btn btn-sm btn-success" data-id="' . $data['employee_name'] . '" data-type="edit" data-route="' . route('unitproduk.edit', ['id' => $data['employee_name']]) . '" data-bs-toggle="modal" data-bs-target="#modal8" data-action="edit" data-title="Unit Produk" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i class="fas fa-pen "></i></button>';
                }
                if ($userauth->can('delete-unit-product')) {
                    $button .= ' <button class="btn btn-sm btn-danger action" data-id="' . $data['employee_name'] . '" data-type="delete" data-route="' . route('unitproduk.delete', ['id' => $data['employee_name']]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i class="fas fa-trash "></i></button>';
                }

                return '<div class="d-flex gap-2">' . $button . '</div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

}
