<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\EmployeeTask;
use App\Models\ReportImage;
use App\Models\TaskReport;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

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
        $data = EmployeeTask::with(['taskDetail', 'employee', 'taskAssign'])->orderByDesc('id')->get();

        return DataTables::of($data)->addIndexColumn()->addColumn('action', function ($group) {
            $userauth = User::with('roles')->where('id', Auth::id())->first();
            $button = '';
            $data = $group;
            if ($userauth->can('update-unit-product')) {
                $button .= ' <a href="' . route('assigmentdata.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-success action mr-1" data-id=' . $data->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Report Data"><i class="fas fa-rocket"></i></a>';
            }
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
        }) ->editColumn('status', function ($data) {
            return $data->getStatus(); // Now this uses the method correctly
        })
        ->rawColumns(['action', 'tugas', 'taskgroup', 'tgl', 'place', 'employee', 'status']) // Add 'status' here
        ->make(true);
    }



    public function show($id)
    {
        $employetask = EmployeeTask::with('taskDetail')->find($id);
        $data = [
            'title' => 'Report ' . $employetask->taskDetail->name,
            'employetask' => $employetask,
        ];
        return view('pages.master.assigmentdata.report', $data);
    }


    public function update($id,Request $request)
    {

        DB::beginTransaction();
        try {
            $taskreport = TaskReport::create([
                'employee_task_id' => $id,
                'report_content' => $request->report_content,
            ]);

            $filebefore = '';
            if ($request->hasFile('before_image')) {
                $file = $request->file('before_image');
                $filebefore = 'report_' . rand(0, 999999999) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/report/'), $filebefore);
            }

            $fileafter = '';
            if ($request->hasFile('after_image')) {
                $file = $request->file('after_image');
                $fileafter = 'report_' . rand(0, 999999999) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/report/'), $fileafter);
            }
            ReportImage::insert([
                [
                    "report_task_id" => $taskreport->id,
                    "report_type" => 'before',
                    "image" => $filebefore,
                ],
                [
                    "report_task_id" => $taskreport->id,
                    "report_type" => 'after',
                    "image"=>$fileafter,
                ],
            ]);

            EmployeeTask::find($id)->update(['status'=>'complated']);
            DB::commit();
            return redirect()->route('assigmentdata');
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'status' => "Gagal",
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }
}
