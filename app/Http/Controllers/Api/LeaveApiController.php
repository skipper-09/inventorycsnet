<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LeaveApiController extends Controller
{
    public function index(Request $request)
    {

        $user = $request->user();
        $data = Leave::where('employee_id', $user->employee_id)->orderByDesc('created_at')->get();
        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil Mendapatkan Data Cuti',
            'data' => $data,
        ]);
    }



    public function ReqLeave(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date|date_format:Y-m-d',
            'end_date' => 'required|date|date_format:Y-m-d|after_or_equal:start_date',
            'reason' => 'required|string|max:255',
        ], [
            'start_date.required' => 'Tanggal mulai harus diisi.',
            'start_date.date' => 'Tanggal mulai harus berupa tanggal yang valid.',
            'start_date.date_format' => 'Format tanggal mulai harus mengikuti format Y-m-d.',
            'end_date.required' => 'Tanggal selesai harus diisi.',
            'end_date.date' => 'Tanggal selesai harus berupa tanggal yang valid.',
            'end_date.date_format' => 'Format tanggal selesai harus mengikuti format Y-m-d.',
            'end_date.after_or_equal' => 'Tanggal selesai harus sama dengan atau setelah tanggal mulai.',
            'reason.required' => 'Alasan harus diisi.',
            'reason.string' => 'Alasan harus berupa teks.',
            'reason.max' => 'Alasan tidak boleh lebih dari 255 karakter.',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }
    
        $user = $request->user();
    
        $year = date('Y', strtotime($request->start_date));
    
        $leave = Leave::create([
            'employee_id' => $user->employee_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'year' => $year,
        ]);
    
        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil Pengajuan Cuti.',
            'data' => $leave,
        ]);
    }
    
    

}
