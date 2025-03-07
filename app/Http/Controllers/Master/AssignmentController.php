<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeTask;
use App\Models\TaskAssign;
use App\Models\TaskDetail;
use App\Models\TaskTemplate;
use App\Models\User;
use App\Notifications\NotificationJobs;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class AssignmentController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Penugasan',
        ];
        return view('pages.master.assignment.index', $data);
    }


    public function getData()
    {
        $data = TaskAssign::with('tasktemplate')->orderByDesc('id')->get();
        $groupedData = $data->groupBy('created_at');

        return DataTables::of($groupedData)->addIndexColumn()->addColumn('action', function ($group) {
            $userauth = User::with('roles')->where('id', Auth::id())->first();
            $button = '';
            $data = $group->first();
            // if ($userauth->can('update-unit-product')) {
            //     $button .= ' <button class="btn btn-sm btn-success" data-id=' . $data->id . ' data-type="edit" data-route="' . route('unitproduk.edit', ['id' => $data->id]) . '" data-proses="' . route('unitproduk.update', ['id' => $data->id]) . '" data-bs-toggle="modal" data-bs-target="#modal8"
            //             data-action="edit" data-title="Unit Produk" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
            //                                         class="fas fa-pen "></i></button>';
            // }
            if ($userauth->can('delete-assignment')) {
                $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('assignment.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
                                                    class="fas fa-trash "></i></button>';
            }
            return '<div class="d-flex gap-2">' . $button . '</div>';
        })->addColumn('name', function ($data) {
            return $data->first()->assignee->name;
        })->addColumn('type', function ($data) {
            return $data->first()->assignee_type == "App\Models\Employee" ? "Karyawan" : "Departement";
        })->addColumn('template', function ($data) {
            return $data->first()->tasktemplate->name;
        })->addColumn('tgl', function ($data) {
            return formatDate($data->first()->assign_date);
        })->addColumn('place', function ($data) {
            return $data->first()->place;
        })->editColumn('assigner', function ($data) {
            return $data->first()->assigner->name;
        })->rawColumns(['action', 'name', 'type', 'template', 'tgl', 'place', 'assigner'])->make(true);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Penugasan',
            'departement' => Department::select('id', 'name')->whereHas('employees')->get(),
            'employee' => Employee::all(),
            'task' => TaskTemplate::has('tasks')->get()
        ];
        return view('pages.master.assignment.add', $data);
    }


    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $tasktemplate = TaskTemplate::find($request->task);
            if (!$tasktemplate) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'status' => "Gagal",
                    'message' => 'Task template not found.'
                ]);
            }

            $assigner = Auth::user(); // User yang mengassign tugas

            // Membuat TaskAssign baru
            $taskassign = TaskAssign::create([
                "task_template_id" => $request->task,
                "assignee_id" => $request->type == "departement" ? $request->departement : $request->employee,
                "assigner_id" => $assigner->id,
                "assignee_type" => $request->type == "departement" ? "App\Models\Department" : "App\Models\Employee",
                'assign_date' => $request->assign_date,
                'place' => $request->place,
            ]);

            DB::commit();

            // Kirimkan notifikasi ke user yang terkait (Assignee adalah User)
            if ($request->type == 'departement') {
                // Jika ditugaskan ke departemen, kirimkan notifikasi kepada setiap karyawan di departemen
                $employees = Employee::where('department_id', $request->departement)->get();
                if ($employees->isEmpty()) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'status' => "Gagal",
                        'message' => 'No employees found in the specified department.'
                    ]);
                }

                foreach ($employees as $employee) {
                    // Mengirim notifikasi kepada User yang terhubung dengan Employee
                    // $user = $employee->user;
                    // if ($user) {
                    //     $user->notify(new NotificationJobs($taskassign));
                    // }

                    foreach ($tasktemplate->tasks as $task) {
                        $taskdetail = TaskDetail::where('task_id', $task->task_id)->get();
                        foreach ($taskdetail as $item) {
                            if (is_object($item) && isset($item)) {
                                EmployeeTask::insert([
                                    'task_assign_id' => $taskassign->id,
                                    'task_detail_id' => $item->id,
                                    'employee_id' => $employee->id
                                ]);
                            } else {
                                DB::rollBack();
                                return response()->json([
                                    'success' => false,
                                    'status' => "Gagal",
                                    'message' => 'Task detail not found for task ID ' . $task->id
                                ]);
                            }
                        }
                    }
                }
            } else {
                // Jika ditugaskan ke individu (employee)
                $user = User::where('employee_id',$request->employee)->first(); // Menemukan user berdasarkan employee_id yang diberikan
                if ($user) {
                    $user->notify(new NotificationJobs($taskassign));
                }

                foreach ($tasktemplate->tasks as $task) {
                    $taskdetail = TaskDetail::where('task_id', $task->task_id)->get();
                    foreach ($taskdetail as $item) {
                        if (is_object($item) && isset($item)) {
                            EmployeeTask::insert([
                                'task_assign_id' => $taskassign->id,
                                'task_detail_id' => $item->id,
                                'employee_id' => $request->employee
                            ]);
                        } else {
                            DB::rollBack();
                            return response()->json([
                                'success' => false,
                                'status' => "Gagal",
                                'message' => 'Task detail not found for task ID ' . $task->id
                            ]);
                        }
                    }
                }
            }

            return redirect()->route('assignment');
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'status' => "Gagal",
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }


    public function destroy($id)
    {
        try {
            $taskassign = TaskAssign::findOrFail($id);
            $taskassign->delete();
            //return response
            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Data Penugasan Berhasil Dihapus!.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal Menghapus Data Penugasan!',
                'trace' => $e->getTrace()
            ]);
        }
    }

}
