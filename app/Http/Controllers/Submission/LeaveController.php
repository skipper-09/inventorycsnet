<?php

namespace App\Http\Controllers\Submission;

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

class LeaveController extends Controller
{
    public function index()
    {
        // Kirimkan data ke view
        $data = [
            "title" => "Pengajuan Cuti",
            "employees" => Employee::all(),
        ];

        return view('pages.submission.leave.index', $data);
    }

    // Menampilkan data cuti dalam bentuk DataTables
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
                if ($userauth->can('update-leave')) {
                    if ($currentUserRole !== 'Employee' || ($currentUserRole === 'Employee' && $data->status !== 'approved')) {
                        $button .= ' <button class="btn btn-sm btn-success" data-id=' . $data->id . ' data-type="edit" data-route="' . route('leave.edit', ['id' => $data->id]) . '" data-proses="' . route('leave.update', ['id' => $data->id]) . '" data-bs-toggle="modal" data-bs-target="#modal8"
                                data-action="edit" data-title="Cuti" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
                                                                class="fas fa-pen "></i></button>';
                    }
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
}