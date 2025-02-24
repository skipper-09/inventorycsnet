<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskTemplate;
use App\Models\Template_task;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class TaskTemplateController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Task Template',
            'taskdata' => Task::where('status', 1)->get(),
        ];
        return view('pages.master.tasktemplate.index', $data);
    }


    public function getData()
    {
        $data = TaskTemplate::with(['tasks.task'])->orderByDesc('id')->get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                $userauth = User::with('roles')->where('id', Auth::id())->first();
                $button = '';

                if ($userauth->can('update-task-template')) {
                    $button .= ' <button class="btn btn-sm btn-success" data-id=' . $data->id . ' data-type="edit" data-route="' . route('tasktemplate.edit', ['id' => $data->id]) . '" data-proses="' . route('tasktemplate.update', ['id' => $data->id]) . '" data-bs-toggle="modal" data-bs-target="#modal8"
                            data-action="edit" data-title="Task Template" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
                                                        class="fas fa-pen "></i></button>';
                }
                $button .= ' <a href="' . route('tasktemplate.detail', ['slug' => $data->slug]) . '" class="btn btn-sm btn-info action mr-1" data-id=' . $data->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Detail Data"><i class="fas fa-eye"></i></a>';
                if ($userauth->can('delete-task-template')) {
                    $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('tasktemplate.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
                                                        class="fas fa-trash "></i></button>';
                }
                return '<div class="d-flex gap-2">' . $button . '</div>';
            })
            ->editColumn('task', function ($data) {
                $taskNames = [];
                foreach ($data->tasks as $task) {
                        $taskNames[] = $task->task->name;
                }
                return implode(', ', $taskNames); 
            })
            ->rawColumns(['action', 'task'])
            ->make(true);
    }



    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
        ], [
            'name.required' => 'Nama Task harus diisi.',
            'description.required' => 'Deskripsi Task harus diisi.',
        ]);

        DB::beginTransaction();
        try {
            $templatetask = TaskTemplate::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            $templateTasks = [];
            foreach ($request->taskdata as $taskId) {
                $templateTasks[] = [
                    'task_template_id' => $templatetask->id,
                    'task_id' => $taskId,
                ];
            }

            Template_task::insert($templateTasks);

            DB::commit();

            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'Template Task Berhasil dibuat.'
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'status' => "Gagal",
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }



    public function show($id)
    {
        $tasktemplate = TaskTemplate::findOrFail($id);
        return response()->json([
            'tasktemplate' => $tasktemplate,
        ], 200);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
        ], [
            'name.required' => 'Nama Task harus diisi.',
            'description.required' => 'Deskripsi Task harus diisi.',
        ]);
        try {
            $unit = TaskTemplate::findOrFail($id);
            $unit->update($request->all());

            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'Taks Template Berhasil diupdate.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => "Gagal",
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }



    public function detail($slug)
    {
        $tasktemplate = TaskTemplate::where('slug', $slug)->first();
        $data = [
            'title' => 'Detail Task Template',
            'tasktempalte' => $tasktemplate->id,
        ];
        return view('pages.master.tasktemplate.detail', $data);
    }




    //destroy data
    public function destroy($id)
    {
        try {
            $tasktemplate = TaskTemplate::findOrFail($id);
            $tasktemplate->delete();
            //return response
            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Task Template Berhasil Dihapus!.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal Menghapus Task Template!',
                'trace' => $e->getTrace()
            ]);
        }
    }
}
