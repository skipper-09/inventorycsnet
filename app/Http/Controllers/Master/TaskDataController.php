<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class TaskDataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $data = [
            'title' => 'Task Data',
        ];
        return view('pages.master.task-data.index', $data);

    }

    public function getData()
    {
        $data = Task::orderByDesc('id')->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $userauth = User::with('roles')->where('id', Auth::id())->first();
            $button = '';

            if ($userauth->can('update-task')) {
                $button .= ' <button class="btn btn-sm btn-success" data-id=' . $data->id . ' data-type="edit" data-route="' . route('taskdata.edit', ['id' => $data->id]) . '" data-proses="' . route('taskdata.update', ['id' => $data->id]) . '" data-bs-toggle="modal" data-bs-target="#modal8"
                            data-action="edit" data-title="Task Data" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
                                                        class="fas fa-pen "></i></button>';
            }
            if ($userauth->can('read-detail-task')) {
                $button .= ' <a href="' . route('taskdetail.index', ['taskdataid' => $data->id]) . '" 
                    class="btn btn-sm btn-info action mr-1" 
                    data-id="' . $data->id . '" 
                    data-type="edit" 
                    data-toggle="tooltip" 
                    data-placement="bottom" 
                    title="Detail Data">
                    <i class="fas fa-eye"></i>
                </a>';
            }
            if ($userauth->can('delete-task')) {
                $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('taskdata.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
                                                        class="fas fa-trash "></i></button>';
            }
            return '<div class="d-flex gap-2">' . $button . '</div>';
        })->editColumn('status', function ($data) {
            $dt = "";
            if ($data->status == 1) {
                $dt = '<span class="badge badge-label-primary">Aktif</span>';
            } else {
                $dt = '<span class="badge badge-label-warning">Tidak Aktif</span>';
            }
            return $dt;
        })->rawColumns(['action', 'status'])->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $request->validate([
        'name' => 'required',
        'description' => 'required',
    ], [
        'name.required' => 'Nama Task harus diisi.',
        'description.required' => 'Deskripsi Task harus diisi.',
    ]);

    try {
        // Create and save the new task
        $taskdata = new Task();
        $taskdata->name = $request->name;
        $taskdata->description = $request->description;
        $taskdata->status = $request->status;
        $taskdata->save();

        //log activity
        activity()
            ->causedBy(Auth::user())
            ->event('created')
            ->withProperties($taskdata->toArray())
            ->log("Task Dibuat dengan Nama {$taskdata->name}");

        return response()->json([
            'success' => true,
            'status' => "Berhasil",
            'message' => 'Task Data Berhasil dibuat.'
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'status' => "Gagal",
            'message' => 'An error occurred: ' . $e->getMessage()
        ]);
    }
}

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $taskdata = Task::findOrFail($id);
        return response()->json([
            'taskdata' => $taskdata,
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
            $taskdata = Task::findOrFail($id);
            $oldTaskData = $taskdata->getAttributes();
            $taskdata->name = $request->name;
            $taskdata->description = $request->description;
            $taskdata->status = $request->status;
            $taskdata->save();

            activity()
            ->causedBy(Auth::user())
            ->event('updated')
            ->withProperties([
                'old' => $oldTaskData,
                'new' => $taskdata->toArray()
            ])
            ->log("Task dengan Nama {$taskdata->name} telah diperbarui.");

            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'Taks Data Berhasil diupdate.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => "Gagal",
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }



    public function detail($id)
    {
        $taskdata = Task::where('id', $id)->first();
        $data = [
            'title' => 'Detail Task ' . $taskdata->name,
            'taskdata' => $taskdata->id,
        ];
        return view('pages.master.task-data.detail', $data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $task = Task::findOrFail($id);
            $task->delete();

            activity()
            ->causedBy(Auth::user())
            ->event('deleted')
            ->withProperties($task->toArray())
            ->log("Task Dihapus dengan Nama {$task->name}");
            //return response
            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Task Data Berhasil Dihapus!.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal Menghapus Task Data!',
                'trace' => $e->getTrace()
            ]);
        }
    }
}
