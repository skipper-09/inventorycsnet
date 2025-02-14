<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class LeaveReportController extends Controller
{
    public function index()
    {
        $status = [
            (object) ['id' => 'approved', 'name' => 'Approved'],
            (object) ['id' => 'pending', 'name' => 'Pending'],
            (object) ['id' => 'rejected', 'name' => 'Rejected'],
        ];
        $data = [
            'title' => 'Cuti Karyawan',
            'status' => $status,
        ];
        return view('pages.report.leave.index', $data);
    }

    //getdata
    public function getData()
    {
        $data = Leave::with(['employee'])->orderByDesc('id')->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $userauth = User::with('roles')->where('id', Auth::id())->first();
            $button = '';
            // $button .= ' <a href="' . route('dashboard') . '" class="btn btn-sm btn-success action mr-1" data-id=' . $data->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
            //                                             class="fas fa-pen "></i></a>';

            if ($userauth->can('update-leave-report')) {
                $button .= ' <button class="btn btn-sm btn-success" data-id=' . $data->id . ' data-type="edit" data-route="' . route('leavereport.edit', ['id' => $data->id]) . '" data-proses="' . route('leavereport.update', ['id' => $data->id]) . '" data-bs-toggle="modal" data-bs-target="#modal8"
                            data-action="edit" data-title="Status" data-toggle="tooltip" data-placement="bottom" title="update Status"><i
                                                        class="fas fa-pen "></i></button>';
            }
            // if ($userauth->can('delete-unit-product')) {
            // $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('unitproduk.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
            //                                             class="fas fa-trash "></i></button>';
            // }
            return '<div class="d-flex gap-2">' . $button . '</div>';
        })->addColumn('name', function ($data) {
            return $data->employee->name;
        })->addColumn('position', function ($data) {
            return $data->employee->position->name;
        })->editColumn('start_date', function ($data) {
            return formatDate($data->start_date);
        })->editColumn('end_date', function ($data) {
            return formatDate($data->end_date);
        })->editColumn('status', function ($data) {
            $dt = "";
            if ($data->status == 'approved') {
                $dt = '<span class="badge badge-primary rounded-pill">Approved</span>';
            } elseif ($data->status == 'pending') {
                $dt = '<span class="badge badge-warning rounded-pill">Pending</span>';
            } else {
                $dt = '<span class="badge badge-danger rounded-pill">Rejected</span>';
            }
            return $dt;
        })->rawColumns(['action', 'name', 'position', 'status'])->make(true);
    }

    public function show($id)
    {
        $leave = Leave::findOrFail($id);
        return response()->json([
            'leave' => $leave,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required',
        ], [
            'status.required' => 'Status harus diisi.',
        ]);
        try {
            $leave = Leave::findOrFail($id);
            $leave->update(['status' => $request->status]);
            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'Status Cuti Karyawan Berhasil diubah ' . $request->status,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => "Gagal",
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }
}
