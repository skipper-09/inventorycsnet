<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskDetail;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class TaskDetailController extends Controller
{
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
                    $button .= ' <button class="btn btn-sm btn-success" 
                        data-id=' . $data->id . ' 
                        data-type="edit" 
                        data-route="' . route('taskdetail.edit', ['id' => $data->id]) . '" 
                        data-proses="' . route('taskdetail.update', ['id' => $data->id]) . '" 
                        data-bs-toggle="modal" 
                        data-bs-target="#modal8" 
                        data-action="edit" 
                        data-title="Task" 
                        data-toggle="tooltip" 
                        data-taskid=' . $data->task_id . ' 
                        data-placement="bottom" 
                        title="Edit Data">
                        <i class="fas fa-pen"></i>
                    </button>';
                }

                // Delete button
                if ($userauth->can('delete-detail-task')) {
                    $button .= ' <button class="btn btn-sm btn-danger action" 
                        data-id=' . $data->id . ' 
                        data-type="delete" 
                        data-route="' . route('taskdetail.delete', ['id' => $data->id]) . '" 
                        data-toggle="tooltip" 
                        data-placement="bottom" 
                        title="Delete Data">
                        <i class="fas fa-trash"></i>
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
            $task = new TaskDetail();
            $task->create([
                'name' => $request->name,
                'description' => $request->description,
                'task_id' => $request->task_data_id
            ]);

            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'Detail Task Berhasil dibuat.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => "Gagal",
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }




    public function show($id)
    {
        $taskdetail = TaskDetail::findOrFail($id);
        return response()->json([
            'taskdetail' => $taskdetail,
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
            $taskdetail = TaskDetail::findOrFail($id);
            $taskdetail->update($request->all());

            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'Detail Task Berhasil diupdate.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => "Gagal",
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }





    //destroy data
    public function destroy($id)
    {
        try {
            $taskdetail = TaskDetail::findOrFail($id);
            $taskdetail->delete();
            //return response
            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Detail Task Berhasil Dihapus!.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal Menghapus Task!',
                'trace' => $e->getTrace()
            ]);
        }
    }

}
