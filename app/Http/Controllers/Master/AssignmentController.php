<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\TaskAssign;
use App\Models\TaskTemplate;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $data = TaskAssign::orderByDesc('id')->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $userauth = User::with('roles')->where('id', Auth::id())->first();
            $button = '';
            // $button .= ' <a href="' . route('dashboard') . '" class="btn btn-sm btn-success action mr-1" data-id=' . $data->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
            //                                             class="fas fa-pen "></i></a>';

            if ($userauth->can('update-unit-product')) {
                $button .= ' <button class="btn btn-sm btn-success" data-id=' . $data->id . ' data-type="edit" data-route="' . route('unitproduk.edit', ['id' => $data->id]) . '" data-proses="' . route('unitproduk.update', ['id' => $data->id]) . '" data-bs-toggle="modal" data-bs-target="#modal8"
                            data-action="edit" data-title="Unit Produk" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
                                                        class="fas fa-pen "></i></button>';
            }
            if ($userauth->can('delete-unit-product')) {
                $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('unitproduk.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
                                                        class="fas fa-trash "></i></button>';
            }
            return '<div class="d-flex gap-2">' . $button . '</div>';
        })->addColumn('name',function($data){
            return $data->assignee->name;
        })->rawColumns(['action','name'])->make(true);
    }


    public function create()
    {
        $data = [
            'title' => 'Tambah Penugasan',
            'departement' => Department::select('id', 'name')->get(),
            'employee' => Employee::all(),
            'task'=>TaskTemplate::all()
        ];
        return view('pages.master.assignment.add', $data);
    }


    public function store(Request $request){
        try {
           foreach ($request->task as $TaskID) {
            TaskAssign::create([
                "task_template_id"=>$TaskID,
                "assignee_id"=>$request->type == "departement" ? $request->departement : $request->employee,
                "assignee_type"=>$request->type =="departement" ? "App\Models\Department" : "App\Models\Employee",
                'assign_date'=>now()
            ]);
           } 
           return redirect()->route('assignment');
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => "Gagal",
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }
}
