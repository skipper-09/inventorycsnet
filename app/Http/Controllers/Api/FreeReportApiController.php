<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FreeReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FreeReportApiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $data = FreeReport::where('user_id', $user->id)->orderByDesc('created_at')->get();
        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil Mendapatkan Data Report Activity',
            'data' => $data,
        ]);
    }


    public function AddFreeReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'report_activity' => 'required|string',
        ], [
            "report_activity.required" => "Aktivitas Wajib dilengkapi."
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        $data = FreeReport::create(
            [
                "report_activity" => $request->report_activity,
                "user_id" => $user->id,
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil Menambahkan Data Report Activity',
            'data' => $data
        ]);
    }
}
