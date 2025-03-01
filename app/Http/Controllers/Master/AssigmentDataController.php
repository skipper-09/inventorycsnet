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
use Illuminate\Support\Facades\Storage;
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
       
        $currentUser = Auth::user();
     
        $currentUserRole = $currentUser->roles->first()?->name;
        if ($currentUserRole == "Employee") {
        $data = EmployeeTask::with(['taskDetail', 'employee', 'taskAssign'])->where('employee_id',$currentUser->employee_id)->orderByDesc('id')->get();
        }else{
            $data = EmployeeTask::with(['taskDetail', 'employee', 'taskAssign'])->orderByDesc('id')->get();
        }

        return DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $userauth = User::with('roles')->where('id', Auth::id())->first();
            $button = '';
           
            if ($data->status != "complated") {
                if ($userauth->can('update-assigmentdata')) {
                    $button .= ' <a href="' . route('assigmentdata.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-success action mr-1" data-id=' . $data->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Report Data"><i class="fas fa-rocket"></i></a>';
                }
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
            return $data->getStatus();
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


    public function update($id, Request $request)
    {
        $request->validate([
            'before_image' => 'required|string', 
            'after_image' => 'required|string',  
        ], [
            'before_image.required' => 'Gambar sebelum harus dilengkapi.',
            'after_image.required' => 'Gambar sesudah harus dilengkapi.',
        ]);
    
        DB::beginTransaction();
        try {
            $taskreport = TaskReport::create([
                'employee_task_id' => $id,
                'report_content' => $request->report_content,
            ]);
    
            // Handle before image
            $filebefore = '';
            if ($request->input('before_image')) {
                $imageData = $request->input('before_image');
                $imageData = str_replace('data:image/png;base64,', '', $imageData);
                $imageData = str_replace('data:image/jpg;base64,', '', $imageData);
                $imageData = str_replace(' ', '+', $imageData);
                $image = base64_decode($imageData);
                $filebefore = 'report_' . rand(0, 999999999) . '.png'; 
    
                Storage::disk('public')->put('report/' . $filebefore, $image);
            }
    
            // Handle after image
            $fileafter = '';
            if ($request->input('after_image')) {
                $imageData = $request->input('after_image');
                $imageData = str_replace('data:image/png;base64,', '', $imageData);
                $imageData = str_replace('data:image/jpg;base64,', '', $imageData);
                $imageData = str_replace(' ', '+', $imageData);
                $image = base64_decode($imageData);
                $fileafter = 'report_' . rand(0, 999999999) . '.png'; 
    
                Storage::disk('public')->put('report/' . $fileafter, $image);
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
                    "image" => $fileafter,
                ],
            ]);
    
            // Update the task status to completed
            // EmployeeTask::find($id)->update(['status' => 'completed']);
            
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
