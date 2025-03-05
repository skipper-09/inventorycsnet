<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
            "employees" => Employee::all(),
        ];

        return view('pages.report.leave.index', $data);
    }

    public function getData()
    {
        $currentUser = Auth::user();
        $currentUserRole = $currentUser->roles->first()?->name;

        // Mengambil data cuti dengan relasi employee
        $query = Leave::with('employee')->orderByDesc('id');

        // Apply role-based filtering
        if ($currentUserRole === 'Employee') {
            // Jika role adalah 'Employee', hanya data cuti yang milik employee yang sedang login
            $query->where('employee_id', $currentUser->employee_id);  // Menggunakan employee_id milik user yang sedang login
        }

        $data = $query->get();

        // Mengembalikan data dengan DataTables
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                $userauth = User::with('roles')->where('id', Auth::id())->first();
                $currentUser = Auth::user();
                $currentUserRole = $currentUser->roles->first()?->name;
                $button = '';
                if ($userauth->can('update-leave-report')) {
                    if ($currentUserRole !== 'Employee' || ($currentUserRole === 'Employee' && $data->status !== 'approved')) {
                        $button .= ' <button class="btn btn-sm btn-success" data-id=' . $data->id . ' data-type="edit" data-route="' . route('leavereport.edit', ['id' => $data->id]) . '" data-proses="' . route('leavereport.update', ['id' => $data->id]) . '" data-bs-toggle="modal" data-bs-target="#modal8"
                                data-action="edit" data-title="Cuti" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
                                                                class="fas fa-pen "></i></button>';
                    }
                }
                if ($userauth->can('delete-leave-report')) {
                    $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('leavereport.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
                                                        class="fas fa-trash "></i></button>';
                }
                return '<div class="d-flex gap-2">' . $button . '</div>';
            })
            ->addColumn('name', function ($data) {
                return $data->employee->name;
            })
            ->addColumn('position', function ($data) {
                return $data->employee->position->name;
            })
            ->editColumn('start_date', function ($data) {
                return Carbon::parse($data->start_date)->format('d F Y');
            })
            ->editColumn('end_date', function ($data) {
                return Carbon::parse($data->end_date)->format('d F Y');
            })
            ->editColumn('status', function ($data) {
                return $data->getStatusBadge($data->status);
            })
            ->rawColumns(['action', 'name', 'position', 'start_date', 'end_date', 'status'])
            ->make(true);
    }

    public function show($id)
    {
        $currentUser = Auth::user();
        $currentUserRole = $currentUser->roles->first()?->name;

        // Find the leave record first
        $leave = Leave::with('employee')->findOrFail($id);

        if ($currentUserRole === 'Employee' && $leave->employee_id !== $currentUser->employee->id) {
            return response()->json([
                'success' => false,
                'message' => 'Data cuti tidak ditemukan',
            ]);
        }

        // Jika role selain Employee atau jika milik Employee yang sedang login, tampilkan data cuti
        return response()->json([
            'success' => true,
            'leave' => $leave,
        ], 200);
    }

    // Make sure the store method is public
    public function store(Request $request)
    {
        $currentUser = Auth::user();
        $currentUserRole = $currentUser->roles->first()?->name;

        // Validation rules
        $rules = [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
            'year' => 'required|integer',
        ];

        // Add employee_id validation for non-Employee roles
        if ($currentUserRole !== 'Employee') {
            $rules['employee_id'] = 'required|exists:employees,id';
            $rules['status'] = 'required|in:pending,approved,rejected';
        }

        $request->validate($rules);

        try {
            $leave = new Leave();
            $leave->start_date = $request->start_date;
            $leave->end_date = $request->end_date;
            $leave->reason = $request->reason;
            $leave->year = $request->year;

            if ($currentUserRole === 'Employee') {
                $leave->employee_id = $currentUser->employee->id;
                $leave->status = 'pending';
            } else {
                $leave->employee_id = $request->employee_id;
                $leave->status = $request->status;
            }

            $leave->save();

            activity()
                ->causedBy(Auth::user())
                ->event('created')
                ->withProperties($leave->toArray())
                ->log("Laporan Cuti berhasil dibuat.");

            return response()->json([
                'success' => true,
                'status' => 'Success',
                'message' => 'Cuti berhasil disimpan.'
            ]);

        } catch (Exception $e) {
            Log::error('Terjadi kesalahan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 422);
        }
    }

    public function update(Request $request, $id)
    {
        $currentUser = Auth::user();
        $currentUserRole = $currentUser->roles->first()?->name;

        // Find the leave record first
        $leave = Leave::findOrFail($id);
        $oldLeave = $leave->getAttributes();

        // Define base validation rules
        $rules = [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
            'year' => 'required|integer|min:2000|max:2099'
        ];

        // Add additional validation rules for non-Employee roles
        if ($currentUserRole !== 'Employee') {
            $rules['employee_id'] = 'required|exists:employees,id';
            $rules['status'] = 'required|in:pending,approved,rejected';
        }

        // Validate request data
        $request->validate($rules);

        try {
            if ($currentUserRole === 'Employee') {
                // Check if the leave belongs to the employee
                if ($leave->employee_id !== $currentUser->employee->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki izin untuk mengubah cuti ini.'
                    ], 403);
                }

                // Check if the leave status is rejected
                if ($leave->status !== 'rejected') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cuti hanya dapat diubah jika statusnya ditolak.'
                    ], 422);
                }

                // Update data for employee
                $leave->update([
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'reason' => $request->reason,
                    'year' => $request->year,
                    'status' => 'pending' // Reset to pending after update
                ]);
            } else {
                // Update data for admin/manager
                $leave->update([
                    'employee_id' => $request->employee_id,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'reason' => $request->reason,
                    'status' => $request->status,
                    'year' => $request->year
                ]);
            }

            activity()
            ->causedBy(Auth::user())
            ->event('updated')
            ->withProperties([
                'old' => $oldLeave,
                'new' => $leave->toArray()
            ])
            ->log("Laporan Cuti berhasil diperbarui.");

            return response()->json([
                'success' => true,
                'status' => 'Success',
                'message' => 'Cuti berhasil diperbarui.'
            ]);

        } catch (Exception $e) {
            Log::error('Error updating leave: ' . $e->getMessage(), [
                'user_id' => $currentUser->id,
                'leave_id' => $id,
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data cuti.'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $leave = Leave::findOrFail($id);
            $leave->delete();

            activity()
            ->causedBy(Auth::user())
            ->event('deleted')
            ->withProperties($leave->toArray())
            ->log("Laaporan Cuti berhasil dihapus.");

            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Cuti berhasil dihapus.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data cuti.'
            ], 500);
        }
    }

    //getdata
    // public function getData()
    // {
    //     $data = Leave::with(['employee'])->orderByDesc('id')->get();
    //     return DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
    //         $userauth = User::with('roles')->where('id', Auth::id())->first();
    //         $button = '';
    //         // $button .= ' <a href="' . route('dashboard') . '" class="btn btn-sm btn-success action mr-1" data-id=' . $data->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
    //         //                                             class="fas fa-pen "></i></a>';

    //         if ($userauth->can('update-leave-report')) {
    //             $button .= ' <button class="btn btn-sm btn-success" data-id=' . $data->id . ' data-type="edit" data-route="' . route('leavereport.edit', ['id' => $data->id]) . '" data-proses="' . route('leavereport.update', ['id' => $data->id]) . '" data-bs-toggle="modal" data-bs-target="#modal8"
    //                         data-action="edit" data-title="Status" data-toggle="tooltip" data-placement="bottom" title="update Status"><i
    //                                                     class="fas fa-pen "></i></button>';
    //         }
    //         // if ($userauth->can('delete-unit-product')) {
    //         // $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('unitproduk.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
    //         //                                             class="fas fa-trash "></i></button>';
    //         // }
    //         return '<div class="d-flex gap-2">' . $button . '</div>';
    //     })->addColumn('name', function ($data) {
    //         return $data->employee->name;
    //     })->addColumn('position', function ($data) {
    //         return $data->employee->position->name;
    //     })->editColumn('start_date', function ($data) {
    //         return formatDate($data->start_date);
    //     })->editColumn('end_date', function ($data) {
    //         return formatDate($data->end_date);
    //     })->editColumn('status', function ($data) {
    //         $dt = "";
    //         if ($data->status == 'approved') {
    //             $dt = '<span class="badge badge-primary rounded-pill">Approved</span>';
    //         } elseif ($data->status == 'pending') {
    //             $dt = '<span class="badge badge-warning rounded-pill">Pending</span>';
    //         } else {
    //             $dt = '<span class="badge badge-danger rounded-pill">Rejected</span>';
    //         }
    //         return $dt;
    //     })->rawColumns(['action', 'name', 'position', 'status'])->make(true);
    // }

    // public function show($id)
    // {
    //     $leave = Leave::findOrFail($id);
    //     return response()->json([
    //         'leave' => $leave,
    //     ], 200);
    // }

    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'status' => 'required',
    //     ], [
    //         'status.required' => 'Status harus diisi.',
    //     ]);
    //     try {
    //         $leave = Leave::findOrFail($id);
    //         $leave->update(['status' => $request->status]);
    //         return response()->json([
    //             'success' => true,
    //             'status' => "Berhasil",
    //             'message' => 'Status Cuti Karyawan Berhasil diubah ' . $request->status,
    //         ]);
    //     } catch (Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'status' => "Gagal",
    //             'message' => 'An error occurred: ' . $e->getMessage()
    //         ]);
    //     }
    // }
}
