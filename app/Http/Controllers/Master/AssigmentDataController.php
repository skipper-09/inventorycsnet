<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\EmployeeTask;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class AssigmentDataController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Tugas Karyawan',
        ];
        return view('pages.master.assigmentdata.index', $data);
    }


    public function getData()
    {
        $data = EmployeeTask::with(['taskDetail','employee','taskAssign'])->orderByDesc('id')->get();
        
        return DataTables::of($data)->addIndexColumn()->addColumn('action', function ($group) {
            $userauth = User::with('roles')->where('id', Auth::id())->first();
            $button = '';
            $data = $group;
            if ($userauth->can('update-unit-product')) {
                $button .= ' <a href="' . route('assigmentdata.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-success action mr-1" data-id=' . $data->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Report Data"><i class="fas fa-rocket"></i></a>';
            }
            // if ($userauth->can('delete-assigment')) {
            //     $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('assignment.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
            //                                         class="fas fa-trash "></i></button>';
            // }
            return '<div class="d-flex gap-2">' . $button . '</div>';
        })->addColumn('tugas', function ($data) {
            return $data->taskDetail->name;
        })->addColumn('taskgroup', function ($data) {
            return $data->taskDetail->task->name;
        })->addColumn('tgl', function ($data) {
            return formatDate($data->taskAssign->assign_date);
        })->addColumn('place', function ($data) {
            return $data->taskAssign->place;
        })->addColumn('employee', function ($data) {
            return $data->employee->name;
        })->rawColumns(['action', 'tugas', 'taskgroup', 'tgl','place','employee' ])->make(true);
    }



    public function show($id)
    {
        $employetask = EmployeeTask::with('taskDetail')->find($id);
        // dd($employetask);
        $data = [
            'title' => 'Report '. $employetask->taskDetail->name,
            'employetask'=> $employetask,
        ];
        return view('pages.master.assigmentdata.report', $data);
    }
}
