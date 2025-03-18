<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmployeeTask;
use App\Models\ReportImage;
use App\Models\TaskAssign;
use App\Models\TaskReport;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AssigmentApiController extends Controller
{

    public function index(Request $request)
    {

        $user = $request->user();
        $query = EmployeeTask::with([
            'employee',
            'taskAssign.tasktemplate',
            'taskDetail.task',
        ])->where('employee_id', $user->employee_id);

        $data = $query->get()->groupBy(function ($item) {
            return $item->task_assign_id;
        });

        $flattenedData = $data->map(function ($group) {
            return $group->first();
        });

        $formattedData = $flattenedData->map(function ($data) {
            return [
                'task_assign_id' => $data->task_assign_id,
                'employee_name' => $data->employee->name ?? 'N/A',
                'assigner_name' => $data->taskAssign->assigner->name ?? 'N/A',
                'assignment_date' => $data->taskAssign->assign_date ?? null,
                'template_tugas' => $data->taskAssign->tasktemplate->name ?? 'N/A',
                'location' => $data->taskAssign->place ?? 'N/A',
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil Mendapatkan Data Tugas',
            'data' => $formattedData,
        ]);
    }


    public function AllTask(Request $request)
    {
        $user = $request->user();
        $task_assign_id = $request->input('task_assign_id');

        $employetask = EmployeeTask::with(['taskDetail.task'])
            ->where('task_assign_id', $task_assign_id)
            ->where('employee_id', $user->employee_id)
            ->get();

        $groupedData = $employetask->groupBy(function ($item) {
            return $item->taskDetail->task->id;
        });

        $data = [];
        foreach ($groupedData as $tasks) {
            $task = $tasks->first()->taskDetail->task;

            $data[] = [
                'task_id' => $task->id,
                'task' => $task->name,
                'task_description' => $task->description,
                'task_details' => $tasks->map(function ($item) {
                    return [
                        'task_detail_id' => $item->taskDetail->id,
                        'task_detail_name' => $item->taskDetail->name,
                        'task_detail_description' => $item->taskDetail->description,
                    ];
                }),
            ];
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil Mendapatkan Data All Task',
            'data' => $data,
        ]);
    }




    public function ReportTask(Request $request, $id)
    {

    dd($request->input('before_image'));
        $validator = Validator::make($request->all(), [
            'before_image' => 'required|file',
            'after_image' => 'required|file',
        ], [
            'before_image.required' => 'Gambar sebelum harus dilengkapi.',
            'after_image.required' => 'Gambar sesudah harus dilengkapi.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $taskreport = TaskReport::create([
            'employee_task_id' => $id,
            'report_content' => $request->report_content,
        ]);

        $filebefore = $this->handleImage($request->before_image);

        $fileafter = $this->handleImage($request->after_image);

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

        EmployeeTask::find($id)->update(['status' => 'in_review']);

        return response()->json([
            'success' => true,
            'status' => 'success',
            'message' => 'Data Assignment berhasil diperbarui.',
            'data' => $taskreport
        ], 200);
    }



    //privat funtion
    private function handleImage($imageData)
    {
        if (!$imageData) {
            return null;
        }

        // Clean up base64 string
        $imageData = preg_replace('/^data:image\/(png|jpg);base64,/', '', $imageData);
        $imageData = str_replace(' ', '+', $imageData);
        $image = base64_decode($imageData);

        // Generate a unique file name
        $fileName = 'report_' . rand(0, 999999999) . '.png';

        // Store the image
        Storage::disk('public')->put('report/' . $fileName, $image);

        return $fileName;
    }

}
