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
            //task diambil hanya yang memiliki detail task dan status aktif
            'taskdata' => Task::where('status', 1)->whereHas('detailtask')->get(),
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
            'taskdata' => 'required',
        ], [
            'name.required' => 'Nama Task harus diisi.',
            'description.required' => 'Deskripsi Task harus diisi.',
            'taskdata.required' => 'Task harus dipilih.',
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
        $tasktemplate = TaskTemplate::with(['tasks.task'])->findOrFail($id);
        return response()->json([
            'tasktemplate' => $tasktemplate,
        ], 200);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'taskdata' => 'required', // Pastikan taskdata juga harus ada saat update
        ], [
            'name.required' => 'Nama Task harus diisi.',
            'description.required' => 'Deskripsi Task harus diisi.',
            'taskdata.required' => 'Task harus dipilih.',
        ]);

        DB::beginTransaction();
        try {
            $tasktemplate = TaskTemplate::findOrFail($id);

            $tasktemplate->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            Template_task::where('task_template_id', $tasktemplate->id)->delete();

            $templateTasks = [];
            foreach ($request->taskdata as $taskId) {
                $templateTasks[] = [
                    'task_template_id' => $tasktemplate->id,
                    'task_id' => $taskId,
                ];
            }

            Template_task::insert($templateTasks);

            DB::commit();

            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'Task Template berhasil diupdate.'
            ]);
        } catch (Exception $e) {
            // Rollback jika terjadi kesalahan
            DB::rollBack();

            return response()->json([
                'success' => false,
                'status' => "Gagal",
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }




    // public function detail($slug)
    // {
    //     $tasktemplate = TaskTemplate::where('slug', $slug)->first();
    //     $data = [
    //         'title' => 'Detail Task Template',
    //         'tasktempalte' => $tasktemplate->id,
    //     ];
    //     return view('pages.master.tasktemplate.detail', $data);
    // }




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
