<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskDetail;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class TaskDetailController extends Controller
{
    public function index($taskdataid)
    {
        $taskdata = Task::where('id', $taskdataid)->first();
        $data = [
            'title' => 'Detail Task',
            'name' => $taskdata->name,
            'taskdata' => $taskdataid,
        ];

        return view('pages.master.task-data.detail.task.index', $data);
    }

    public function getData(Request $request, $taskdataid)
    {
        $data = TaskDetail::where('task_id', $taskdataid)->orderByDesc('id')->get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                $userauth = User::with('roles')->where('id', Auth::id())->first();
                $button = '';

                // View Description button (only if description length > 50)
                $stripped = strip_tags($data->description);
                if (strlen($stripped) > 50) {
                    $button .= ' <button class="btn btn-sm btn-info show-full-description" 
                            data-bs-toggle="modal" 
                            data-bs-target="#viewDescriptionModal" 
                            data-description="' . htmlspecialchars($data->description, ENT_QUOTES) . '" 
                            data-toggle="tooltip" 
                            data-placement="bottom" 
                            title="View Description">
                            <i class="fas fa-eye"></i>
                        </button>';
                }

                // Edit button
                if ($userauth->can(abilities: 'update-detail-task')) {
                    $button .= '<a href="' . route('taskdetail.edit', ['id' => $data->id]) . '"
                      class="btn btn-sm btn-success"
                       data-id="' . $data->id . '"
                       data-type="edit"
                       data-toggle="tooltip"
                       data-placement="bottom"
                       title="Edit Data">
                       <i class="fas fa-pen"></i>
                    </a>';
                }

                // Delete button
                if ($userauth->can('delete-detail-task')) {
                    $button .= ' <button class="btn btn-sm btn-danger action"
                            data-id="' . $data->id . '"
                            data-type="delete"
                            data-route="' . route('taskdetail.delete', ['id' => $data->id]) . '"
                            data-toggle="tooltip"
                            data-placement="bottom"
                            title="Delete Data">
                        <i class="fas fa-trash-alt"></i>
                    </button>';
                }

                return '<div class="d-flex gap-2">' . $button . '</div>';
            })
            ->editColumn('description', function ($data) {
                $stripped = strip_tags($data->description);
                return Str::limit($stripped, 100); // Return a shortened version of the description
            })
            ->rawColumns(['action', 'description'])
            ->make(true);
    }

    public function create($taskdataid)
    {
        $data = [
            'title' => 'Add Detail Task',
            'taskdata' => $taskdataid,  // Pass the task_id to the view
        ];

        return view('pages.master.task-data.detail.task.add', $data);
    }

    public function store(Request $request, $taskdataid)
    {

        $request->validate([
            'name' => 'required',
            'description' => 'required',
        ], [
            'name.required' => 'Nama Task harus diisi.',
            'description.required' => 'Deskripsi Task harus diisi.',
        ]);

        try {
            $task = new TaskDetail();

            $task->fill([
                'name' => $request->name,
                'description' => $request->description,
                'task_id' => $taskdataid,
            ]);

            $task->save();

            activity()
                ->causedBy(Auth::user())
                ->event('created')
                ->withProperties($task->toArray())
                ->log("Detail Task berhasil dibuat.");

            return redirect()->route('taskdetail.index', ['taskdataid' => $taskdataid])->with([
                'status' => 'Success!',
                'message' => 'Berhasil Menambahkan Detail Task!'
            ]);
        } catch (Exception $e) {
            return redirect()->route('taskdetail.index', ['taskdataid' => $taskdataid])->with([
                'status' => 'Error!',
                'message' => 'Gagal Menambahkan Detail Task!'
            ]);
        }
    }

    public function edit($id)
    {
        $taskdetail = TaskDetail::findOrFail($id);

        $data = [
            'title' => 'Edit Detail Task',
            'taskdata' => $taskdetail,  // fetching the correct task detail
        ];

        return view('pages.master.task-data.detail.task.edit', $data);
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
            $taskdetail = TaskDetail::findOrFail($id);
            $oldTaskDetail = $taskdetail->toArray();

            $taskdetail->update($request->all());

            activity()
                ->causedBy(Auth::user())
                ->event('updated')
                ->withProperties([
                    'old' => $oldTaskDetail,
                    'new' => $taskdetail->toArray()
                ])
                ->log("Detail Task berhasil diperbarui.");

            return redirect()->route('taskdetail.index', ['taskdataid' => $taskdetail->task_id])->with([
                'status' => 'Success!',
                'message' => 'Berhasil Mengubah Detail Task!'
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $taskdetail = TaskDetail::findOrFail($id);
            return redirect()->route('taskdetail.index', ['taskdataid' => $taskdetail->task_id])->with([
                'status' => 'Error!',
                'message' => 'Gagal Mengubah Detail Task!'
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $taskdetail = TaskDetail::findOrFail($id);

            activity()
                ->causedBy(Auth::user())
                ->event('deleted')
                ->withProperties($taskdetail->toArray())
                ->log("Detail Task berhasil dihapus.");

            $taskdetail->delete();

            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Detail Task Berhasil Dihapus!.',
            ]);
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return response()->json([
                'message' => 'Gagal Menghapus Task!',
                'trace' => $e->getTrace()
            ]);
        }
    }
}