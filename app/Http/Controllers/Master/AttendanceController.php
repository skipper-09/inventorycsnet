<?php

namespace App\Http\Controllers\Master;

use App\Exports\AttendanceReportExport;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\WorkSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceController extends Controller
{
    public function index()
    {
        $data = [
            "title" => "Absensi Karyawan",
            "employees" => Employee::all(),
        ];

        return view("pages.master.attendance.index", $data);
    }

    public function getData(Request $request)
    {
        $query = Attendance::with('employee', 'workSchedule')
            ->orderByDesc('created_at');

        // Apply date filter
        if ($request->filled('filter_date')) {
            $date = Carbon::parse($request->filter_date)->format('Y-m-d');
            $query->whereDate('created_at', $date);
        }

        // Apply employee filter
        if ($request->filled('filter_employee')) {
            $query->where('employee_id', $request->filter_employee);
        }

        // Apply status filter
        if ($request->filled('filter_status')) {
            switch ($request->filter_status) {
                case 'late_in':
                    $query->where('clock_in_status', 'late');
                    break;
                case 'early_out':
                    $query->where('clock_out_status', 'early');
                    break;
                case 'on_time':
                    $query->where('clock_in_status', '!=', 'late')
                        ->where(function ($q) {
                            $q->whereNull('clock_out')
                                ->orWhere('clock_out_status', '!=', 'early');
                        });
                    break;
                case 'no_checkout':
                    $query->whereNull('clock_out');
                    break;
            }
        }

        return DataTables::of($query)
            ->addColumn('employee_name', function ($data) {
                return $data->employee->name ?? 'N/A';
            })
            ->addColumn('date', function ($data) {
                return Carbon::parse($data->created_at)->format('d F Y');
            })
            ->addColumn('schedule', function ($data) {
                if ($data->workSchedule && $data->workSchedule->shift) {
                    return $data->workSchedule->shift->name ?? 'N/A';
                }
                return 'N/A';
            })
            ->addColumn('clock_in_time', function ($data) {
                return $data->clock_in;
            })
            ->addColumn('clock_out_time', function ($data) {
                return $data->clock_out ?? 'Belum Checkout';
            })
            ->addColumn('status', function ($data) {
                $clockInStatus = '';
                $clockOutStatus = '';

                if ($data->clock_in_status == 'late') {
                    $clockInStatus = '<span class="badge bg-warning">Terlambat</span>';
                } else {
                    $clockInStatus = '<span class="badge bg-success">Tepat Waktu</span>';
                }

                if ($data->clock_out) {
                    if ($data->clock_out_status == 'early') {
                        $clockOutStatus = '<span class="badge bg-warning">Pulang Awal</span>';
                    } else {
                        $clockOutStatus = '<span class="badge bg-success">Tepat Waktu</span>';
                    }
                } else {
                    $clockOutStatus = '<span class="badge bg-secondary">Belum Checkout</span>';
                }

                return $clockInStatus . ' ' . $clockOutStatus;
            })
            ->addColumn('action', function ($data) {
                $userauth = Auth::user();
                $button = '';

                if ($userauth->can('read-attendance')) {
                    $button .= '<a href="' . route('attendance.details', ['id' => $data->id]) . '"
                      class="btn btn-sm btn-info"
                       data-id="' . $data->id . '"
                       data-type="details"
                       data-toggle="tooltip"
                       data-placement="bottom"
                       title="Details">
                       <i class="fas fa-eye"></i>
                   </a>';
                }
                if ($userauth->can('update-attendance')) {
                    $button .= '<a href="' . route('attendance.edit', ['id' => $data->id]) . '"
                      class="btn btn-sm btn-success"
                       data-id="' . $data->id . '"
                       data-type="edit"
                       data-toggle="tooltip"
                       data-placement="bottom"
                       title="Edit Data">
                       <i class="fas fa-pen"></i>
                   </a>';
                }
                if ($userauth->can('delete-attendance')) {
                    $button .= ' <button class="btn btn-sm btn-danger action"
                            data-id="' . $data->id . '"
                            data-type="delete"
                            data-route="' . route('attendance.delete', ['id' => $data->id]) . '"
                            data-toggle="tooltip"
                            data-placement="bottom"
                            title="Delete Data">
                        <i class="fas fa-trash-alt"></i>
                    </button>';
                }
                return '<div class="d-flex gap-2">' . $button . '</div>';
            })
            ->rawColumns(['action', 'status', 'employee_name', 'date', 'clock_in_time', 'clock_out_time'])
            ->make(true);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Absensi',
            'employees' => Employee::orderBy('name')->get(),
            'workSchedules' => [], // Initialize as empty array, will be populated via AJAX
            'today' => Carbon::now()->format('Y-m-d'),
        ];

        return view('pages.master.attendance.add', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'work_schedule_id' => 'nullable|exists:work_schedules,id',
            'clock_in' => 'required',
            'clock_out' => 'nullable',
            'clock_in_status' => 'required|in:normal,late',
            'clock_out_status' => 'nullable|in:normal,early',
            'clock_in_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'clock_out_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            // Check if attendance for this employee today already exists
            $date = Carbon::parse($request->attendance_date ?? Carbon::now()->format('Y-m-d'));
            $existingAttendance = Attendance::where('employee_id', $request->employee_id)
                ->whereDate('created_at', $date->format('Y-m-d'))
                ->first();

            if ($existingAttendance) {
                throw ValidationException::withMessages([
                    'employee_id' => 'Absensi untuk karyawan ini hari ini sudah ada.'
                ]);
            }

            // Process the clock in image
            $clockInImage = null;
            if ($request->hasFile('clock_in_image')) {
                $clockInFile = $request->file('clock_in_image');
                $clockInFileName = time() . '_in_' . $request->employee_id . '.' . $clockInFile->getClientOriginalExtension();
                $clockInPath = $clockInFile->storeAs('attendance', $clockInFileName, 'public');
                $clockInImage = $clockInPath;
            }

            // Process the clock out image if exists
            $clockOutImage = null;
            $clockOutStatus = null;

            if ($request->filled('clock_out') && $request->hasFile('clock_out_image')) {
                $clockOutFile = $request->file('clock_out_image');
                $clockOutFileName = time() . '_out_' . $request->employee_id . '.' . $clockOutFile->getClientOriginalExtension();
                $clockOutPath = $clockOutFile->storeAs('attendance', $clockOutFileName, 'public');
                $clockOutImage = $clockOutPath;
                $clockOutStatus = $request->clock_out_status;
            }

            // Create attendance record
            $attendance = new Attendance([
                'employee_id' => $request->employee_id,
                'work_schedule_id' => $request->work_schedule_id,
                'clock_in' => $request->clock_in,
                'clock_out' => $request->filled('clock_out') ? $request->clock_out : null,
                'clock_in_status' => $request->clock_in_status,
                'clock_out_status' => $clockOutStatus,
                'clock_in_image' => $clockInImage,
                'clock_out_image' => $clockOutImage,
            ]);

            // Set the created_at to match the attendance date if provided
            if ($request->has('attendance_date')) {
                $attendance->created_at = $date;
            }

            $attendance->save();

            activity()
                ->causedBy(Auth::user())
                ->event('created')
                ->withProperties($attendance->toArray())
                ->log("Absensi Baru Ditambahkan");

            return redirect()->route('attendance')->with([
                'status' => 'Success!',
                'message' => 'Berhasil Menambahkan Absensi!'
            ]);
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            Log::error('AttendanceController@store: ' . $e->getMessage());
            return redirect()->back()->with([
                'status' => 'Error!',
                'message' => 'Gagal Menambahkan Absensi: ' . $e->getMessage()
            ])->withInput();
        }
    }

    public function getEmployeeSchedules(Request $request)
    {
        try {
            $employeeId = $request->employee_id;
            $date = $request->date ?? Carbon::now()->format('Y-m-d');

            $schedules = WorkSchedule::with('shift')
                ->where('employee_id', $employeeId)
                ->where('schedule_date', $date)
                ->get();


            // Log::info('AttendanceController@getEmployeeSchedules: ' . $schedules);    

            return response()->json([
                'success' => true,
                'data' => $schedules
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function details($id)
    {
        try {
            $attendance = Attendance::with('employee', 'workSchedule.shift')->findOrFail($id);

            $data = [
                'title' => 'Detail Absensi',
                'attendance' => $attendance,
            ];

            return view('pages.master.attendance.details', $data);
        } catch (Exception $e) {
            Log::error('AttendanceController@show: ' . $e->getMessage());
            return redirect()->route('attendance.index')->with([
                'status' => 'Error!',
                'message' => 'Absensi tidak ditemukan'
            ]);
        }
    }

    public function show($id)
    {
        try {
            $attendance = Attendance::with('employee', 'workSchedule')->findOrFail($id);

            $data = [
                'title' => 'Edit Absensi',
                'attendance' => $attendance,
                'employees' => Employee::orderBy('name')->get(),
                'workSchedules' => WorkSchedule::with('shift')
                    ->where('employee_id', $attendance->employee_id)
                    ->whereDate('schedule_date', Carbon::parse($attendance->created_at)->format('Y-m-d'))
                    ->get(),
                'today' => Carbon::parse($attendance->created_at)->format('Y-m-d'),
            ];

            return view('pages.master.attendance.edit', $data);
        } catch (Exception $e) {
            Log::error('AttendanceController@edit: ' . $e->getMessage());
            return redirect()->route('attendance')->with([
                'status' => 'Error!',
                'message' => 'Absensi tidak ditemukan'
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'work_schedule_id' => 'nullable|exists:work_schedules,id',
            'clock_in' => 'required',
            'clock_out' => 'nullable',
            'clock_in_status' => 'required|in:normal,late',
            'clock_out_status' => 'nullable|in:normal,early',
            'clock_in_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'clock_out_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $attendance = Attendance::findOrFail($id);
            $oldAttendance = $attendance->toArray();

            // Process clock in image if new one provided
            $clockInImage = $attendance->clock_in_image;
            if ($request->hasFile('clock_in_image')) {
                // Delete old image if exists
                if ($attendance->clock_in_image) {
                    Storage::disk('public')->delete($attendance->clock_in_image);
                }

                $clockInFile = $request->file('clock_in_image');
                $clockInFileName = time() . '_in_' . $request->employee_id . '.' . $clockInFile->getClientOriginalExtension();
                $clockInPath = $clockInFile->storeAs('attendance', $clockInFileName, 'public');
                $clockInImage = $clockInPath;
            }

            // Process clock out image if new one provided
            $clockOutImage = $attendance->clock_out_image;
            if ($request->hasFile('clock_out_image')) {
                // Delete old image if exists
                if ($attendance->clock_out_image) {
                    Storage::disk('public')->delete($attendance->clock_out_image);
                }

                $clockOutFile = $request->file('clock_out_image');
                $clockOutFileName = time() . '_out_' . $request->employee_id . '.' . $clockOutFile->getClientOriginalExtension();
                $clockOutPath = $clockOutFile->storeAs('attendance', $clockOutFileName, 'public');
                $clockOutImage = $clockOutPath;
            }

            $clockOutStatus = null;
            if ($request->filled('clock_out')) {
                $clockOutStatus = $request->clock_out_status;
            }

            // Update the attendance
            $attendance->update([
                'employee_id' => $request->employee_id,
                'work_schedule_id' => $request->work_schedule_id,
                'clock_in' => $request->clock_in,
                'clock_out' => $request->filled('clock_out') ? $request->clock_out : null,
                'clock_in_status' => $request->clock_in_status,
                'clock_out_status' => $clockOutStatus,
                'clock_in_image' => $clockInImage,
                'clock_out_image' => $clockOutImage,
            ]);

            // Update the created_at if attendance date is provided
            if ($request->has('attendance_date')) {
                $date = Carbon::parse($request->attendance_date);
                $attendance->created_at = $date;
                $attendance->save();
            }

            activity()
                ->causedBy(Auth::user())
                ->event('updated')
                ->withProperties([
                    'old' => $oldAttendance,
                    'new' => $attendance->toArray()
                ])
                ->log("Absensi Berhasil Diperbarui");

            return redirect()->route('attendance')->with([
                'status' => 'Success!',
                'message' => 'Berhasil Mengupdate Absensi!'
            ]);
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            Log::error('AttendanceController@update: ' . $e->getMessage());
            return redirect()->back()->with([
                'status' => 'Error!',
                'message' => 'Gagal Mengupdate Absensi: ' . $e->getMessage()
            ])->withInput();
        }
    }

    public function clockOut(Request $request, $id)
    {
        $request->validate([
            'clock_out' => 'required',
            'clock_out_status' => 'required|in:normal,early',
            'clock_out_image' => 'required',
        ]);

        try {
            $attendance = Attendance::findOrFail($id);

            if ($attendance->clock_out) {
                throw ValidationException::withMessages([
                    'clock_out' => 'Karyawan ini sudah melakukan clock out.'
                ]);
            }

            $oldAttendance = $attendance->toArray();

            // Update with clock out data
            $attendance->update([
                'clock_out' => $request->clock_out,
                'clock_out_status' => $request->clock_out_status,
                'clock_out_image' => $request->clock_out_image,
            ]);

            $attendance->refresh();

            activity()
                ->causedBy(Auth::user())
                ->event('updated')
                ->withProperties([
                    'old' => $oldAttendance,
                    'new' => $attendance->toArray()
                ])
                ->log("Clock Out Berhasil");

            return response()->json([
                'success' => true,
                'message' => 'Clock Out Berhasil!',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->errors(),
            ]);
        } catch (Exception $e) {
            Log::error('AttendanceController@clockOut: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan clock out: ' . $e->getMessage(),
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $attendance = Attendance::findOrFail($id);

            activity()
                ->causedBy(Auth::user())
                ->event('deleted')
                ->withProperties($attendance->toArray())
                ->log("Absensi Berhasil Dihapus");

            $attendance->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Data Absensi Berhasil Dihapus!'
            ]);
        } catch (Exception $e) {
            Log::error('AttendanceController@destroy: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal Menghapus Data Absensi!',
                'trace' => $e->getTrace() // Return trace for debugging purposes
            ]);
        }
    }

    public function exportAttendance(Request $request)
    {
        $query = Attendance::with(['employee', 'workSchedule.shift'])
            ->orderByDesc('created_at');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // if ($request->filled('status')) {
        //     switch ($request->status) {
        //         case 'late':
        //             $query->where('clock_in_status', 'late');
        //             break;
        //         case 'early':
        //             $query->where('clock_out_status', 'early');
        //             break;
        //         case 'normal':
        //             $query->where('clock_in_status', '!=', 'late')
        //                 ->where(function ($q) {
        //                     $q->whereNull('clock_out')
        //                         ->orWhere('clock_out_status', '!=', 'early');
        //                 });
        //             break;
        //         case 'no_checkout':
        //             $query->whereNull('clock_out');
        //             break;
        //     }
        // }

        $data = $query->get();

        $filename = 'attendance_report';

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDateFormat = Carbon::parse($request->start_date)->format('d_M_Y');
            $endDateFormat = Carbon::parse($request->end_date)->format('d_M_Y');
            $filename .= '_' . $startDateFormat . '_to_' . $endDateFormat;
        }

        if ($request->filled('employee_id')) {
            $employee = Employee::find($request->employee_id);
            if ($employee) {
                $filename .= '_' . str_replace(' ', '_', $employee->name);
            }
        }

        $filename .= '.xlsx';

        return Excel::download(new AttendanceReportExport($data), $filename);
    }

    // public function report()
    // {
    //     $data = [
    //         "title" => "Laporan Absensi",
    //         "employees" => Employee::orderBy('name')->get(),
    //     ];

    //     return view("pages.master.attendance.report", $data);
    // }
}