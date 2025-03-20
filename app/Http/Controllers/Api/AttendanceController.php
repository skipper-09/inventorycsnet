<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceNotes;
use App\Models\Attendatelocation;
use App\Models\Employee;
use App\Models\Office;
use App\Models\WorkSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Exception;
use Illuminate\Validation\ValidationException;

class AttendanceController extends Controller
{
    public function index()
    {
        // Existing code remains the same
        try {
            $user = Auth::user();

            $query = Attendance::with('employee', 'workSchedule.shift')
                ->orderByDesc('created_at');

            // If user has Employee role, restrict to their own data
            if ($user->hasRole('Employee')) {
                $query->where('employee_id', $user->employee_id);
            }

            // Get all attendance data without pagination
            $attendances = $query->get();

            return response()->json([
                'success' => true,
                'data' => $attendances,
            ]);
        } catch (Exception $e) {
            Log::error('AttendanceApiController@index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve attendance data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get attendance data for the current day with filtering options
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTodayAttendances(Request $request)
    {
        try {
            $user = Auth::user();
            $today = Carbon::now()->format('Y-m-d');

            // Build the query for today's attendances
            $query = Attendance::with(['employee', 'workSchedule.shift', 'attendanceNotes'])
                ->whereDate('created_at', $today);

            // If user has Employee role, restrict to their own data
            if ($user->hasRole('Employee')) {
                $query->where('employee_id', $user->employee_id);
            }

            // Filter by clock-in status
            if ($request->has('has_clock_in') && $request->has_clock_in !== null) {
                if ($request->has_clock_in) {
                    $query->whereNotNull('clock_in');
                } else {
                    $query->whereNull('clock_in');
                }
            }

            // Filter by clock-out status
            if ($request->has('has_clock_out') && $request->has_clock_out !== null) {
                if ($request->has_clock_out) {
                    $query->whereNotNull('clock_out');
                } else {
                    $query->whereNull('clock_out');
                }
            }

            // Get the results
            $attendances = $query->get();

            // Enhanced data processing
            $processedAttendances = $attendances->map(function ($attendance) {
                $clockIn = $attendance->clock_in ? Carbon::parse($attendance->created_at->format('Y-m-d') . ' ' . $attendance->clock_in) : null;
                $clockOut = $attendance->clock_out ? Carbon::parse($attendance->created_at->format('Y-m-d') . ' ' . $attendance->clock_out) : null;

                // Calculate work duration if both clock-in and clock-out exist
                $workDuration = null;
                if ($clockIn && $clockOut) {
                    $durationMinutes = $clockOut->diffInMinutes($clockIn);
                    $hours = floor($durationMinutes / 60);
                    $minutes = $durationMinutes % 60;
                    $workDuration = $hours . ' jam ' . $minutes . ' menit';
                }

                // Get location data for this attendance
                $locations = Attendatelocation::where('attendance_id', $attendance->id)->get();
                $clockInLocation = $locations->where('attendance_type', 'in')->first();
                $clockOutLocation = $locations->where('attendance_type', 'out')->first();

                // Get notes for this attendance
                $clockInNotes = AttendanceNotes::where('attendance_id', $attendance->id)
                    ->where('attendance_type', 'in')
                    ->first();

                $clockOutNotes = AttendanceNotes::where('attendance_id', $attendance->id)
                    ->where('attendance_type', 'out')
                    ->first();

                return [
                    'id' => $attendance->id,
                    'employee' => [
                        'id' => $attendance->employee->id,
                        'name' => $attendance->employee->name,
                        'position' => $attendance->employee->position,
                        'department' => $attendance->employee->department,
                    ],
                    'clock_in' => [
                        'time' => $attendance->clock_in,
                        'status' => $attendance->clock_in_status,
                        'image' => $attendance->clock_in_image ? url('storage/' . $attendance->clock_in_image) : null,
                        'location' => $clockInLocation ? [
                            'lat' => $clockInLocation->lat,
                            'long' => $clockInLocation->long,
                            'status' => $clockInLocation->status
                        ] : null,
                        'notes' => $clockInNotes ? $clockInNotes->notes : null,
                    ],
                    'clock_out' => $attendance->clock_out ? [
                        'time' => $attendance->clock_out,
                        'status' => $attendance->clock_out_status,
                        'image' => $attendance->clock_out_image ? url('storage/' . $attendance->clock_out_image) : null,
                        'location' => $clockOutLocation ? [
                            'lat' => $clockOutLocation->lat,
                            'long' => $clockOutLocation->long,
                            'status' => $clockOutLocation->status
                        ] : null,
                        'notes' => $clockOutNotes ? $clockOutNotes->notes : null,
                    ] : null,
                    'work_duration' => $workDuration,
                    'work_schedule' => $attendance->workSchedule ? [
                        'id' => $attendance->workSchedule->id,
                        'shift_name' => $attendance->workSchedule->shift->name ?? null,
                        'shift_start' => $attendance->workSchedule->shift->shift_start ?? null,
                        'shift_end' => $attendance->workSchedule->shift->shift_end ?? null,
                    ] : null,
                    'created_at' => $attendance->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $attendance->updated_at->format('Y-m-d H:i:s'),
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Data absensi hari ini berhasil diambil',
                'date' => $today,
                'count' => $processedAttendances->count(),
                'data' => $processedAttendances
            ]);
        } catch (Exception $e) {
            Log::error('AttendanceApiController@getTodayAttendances: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data absensi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clock in API endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function clockIn(Request $request)
    {
        $request->validate([
            'work_schedule_id' => 'nullable|exists:work_schedules,id',
            'clock_in_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        try {
            $user = Auth::user();
            if (!$user->employee_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terkait dengan karyawan manapun.'
                ], 400);
            }

            $employeeId = $user->employee_id;

            // Check if attendance for this employee today already exists
            $existingAttendance = Attendance::where('employee_id', $employeeId)
                ->whereDate('created_at', Carbon::now()->format('Y-m-d'))
                ->first();

            if ($existingAttendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Absensi untuk karyawan ini hari ini sudah ada.'
                ], 422);
            }

            // Get the current time for clock in
            $currentTime = Carbon::now();
            $clockInTime = $currentTime->format('H:i:s');

            // Get the work schedule for the employee
            $workScheduleId = $request->work_schedule_id;
            $workSchedule = null;

            // If work_schedule_id is not provided, find today's work schedule for the employee
            if (!$workScheduleId) {
                $workSchedule = WorkSchedule::with('shift')
                    ->where('employee_id', $employeeId)
                    ->where('schedule_date', $currentTime->format('Y-m-d'))
                    ->first();

                if ($workSchedule) {
                    $workScheduleId = $workSchedule->id;
                }
            } else {
                // If work_schedule_id is provided, get the work schedule data
                $workSchedule = WorkSchedule::with('shift')->find($workScheduleId);

                // Check if this work schedule belongs to the employee who is clocking in
                if ($workSchedule && $workSchedule->employee_id != $employeeId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Jadwal kerja ini bukan milik karyawan yang sedang clock in.'
                    ], 422);
                }
            }

            // Calculate if the clock in is late based on the work schedule
            $clockInStatus = 'normal';
            $isLate = false;
            $shiftMatchMessage = 'Tidak ada jadwal shift untuk hari ini';

            if ($workSchedule && $workSchedule->shift) {
                $shift = $workSchedule->shift;
                $shiftStartTime = Carbon::parse($currentTime->format('Y-m-d') . ' ' . $shift->shift_start);

                // Check if clock in is late
                if ($currentTime->gt($shiftStartTime)) {
                    $clockInStatus = 'late';
                    $isLate = true;
                }

                // Calculate time difference between shift start and clock in
                $diffInMinutes = $currentTime->diffInMinutes($shiftStartTime);

                // Shift match message
                if ($isLate) {
                    $shiftMatchMessage = "Clock in terlambat $diffInMinutes menit dari jadwal shift";
                } else {
                    $shiftMatchMessage = "Clock in lebih awal $diffInMinutes menit dari jadwal shift";
                }
            }

            // Process the clock in image
            $clockInFile = $request->file('clock_in_image');
            $clockInFileName = time() . '_in_' . $employeeId . '.' . $clockInFile->getClientOriginalExtension();
            $clockInPath = $clockInFile->storeAs('attendance', $clockInFileName, 'public');

            // Create attendance record
            $attendance = new Attendance([
                'employee_id' => $employeeId,
                'work_schedule_id' => $workScheduleId,
                'clock_in' => $clockInTime,
                'clock_in_status' => $clockInStatus,
                'clock_in_image' => $clockInPath,
            ]);

            $attendance->save();

            // Check employee location against office locations
            $locationData = $this->checkEmployeeLocation($employeeId, $request->latitude, $request->longitude);

            // Add entry to the attendatelocations table
            $attendanceLocation = new Attendatelocation([
                'attendance_id' => $attendance->id,
                'lat' => $request->latitude ?? '',
                'long' => $request->longitude ?? '',
                'status' => $locationData['status'],
                'attendance_type' => 'in'
            ]);

            $attendanceLocation->save();

            // Prepare reasons if late or offsite
            $noteReasons = [];
            if ($isLate) {
                $noteReasons[] = 'Telat';
            }
            if ($locationData['isOffsite']) {
                $noteReasons[] = 'Di luar lokasi kantor';
            }

            // Log the activity
            activity()
                ->causedBy(Auth::check() ? Auth::user() : null)
                ->performedOn($attendance)
                ->event('created')
                ->withProperties($attendance->toArray())
                ->log("Clock In Berhasil");

            return response()->json([
                'success' => true,
                'message' => 'Clock In berhasil',
                'data' => [
                    'attendance_id' => $attendance->id,
                    'employee_id' => $attendance->employee_id,
                    'clock_in_time' => $attendance->clock_in,
                    'clock_in_status' => $attendance->clock_in_status,
                    'location_status' => $locationData['status'],
                    'shift_match' => $shiftMatchMessage,
                    'require_notes' => ($isLate || $locationData['isOffsite']),
                    'note_reasons' => !empty($noteReasons) ? $noteReasons : null,
                    'work_schedule' => $workSchedule ? [
                        'id' => $workSchedule->id,
                        'shift_name' => $workSchedule->shift->name ?? null,
                        'shift_start' => $workSchedule->shift->shift_start ?? null,
                        'shift_end' => $workSchedule->shift->shift_end ?? null,
                    ] : null
                ]
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            Log::error('AttendanceApiController@clockIn: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan clock in: ' . $e->getMessage()
            ], 500);
        }
    }

     /**
     * Clock out API endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function clockOut(Request $request)
    {
        $request->validate([
            'clock_out_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        try {
            $user = Auth::user();
            if (!$user->employee_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terkait dengan karyawan manapun.'
                ], 400);
            }

            $employeeId = $user->employee_id;

            // Find today's attendance for the employee
            $attendance = Attendance::where('employee_id', $employeeId)
                ->whereDate('created_at', Carbon::now()->format('Y-m-d'))
                ->first();

            if (!$attendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada absensi clock in hari ini untuk karyawan ini.'
                ], 404);
            }

            if ($attendance->clock_out) {
                return response()->json([
                    'success' => false,
                    'message' => 'Karyawan ini sudah melakukan clock out hari ini.'
                ], 422);
            }

            // Get current time
            $currentTime = Carbon::now();
            $clockOutTime = $currentTime->format('H:i:s');

            // Calculate if the clock out is early based on the work schedule
            $clockOutStatus = 'normal';
            $workSchedule = null;
            $isEarly = false;

            if ($attendance->work_schedule_id) {
                $workSchedule = WorkSchedule::with('shift')->find($attendance->work_schedule_id);
                if ($workSchedule && $workSchedule->shift) {
                    $shiftEndTime = Carbon::parse($currentTime->format('Y-m-d') . ' ' . $workSchedule->shift->shift_end);

                    if ($currentTime->lt($shiftEndTime)) {
                        $clockOutStatus = 'early';
                        $isEarly = true;
                    }
                }
            }

            // Process the clock out image
            $clockOutFile = $request->file('clock_out_image');
            $clockOutFileName = time() . '_out_' . $employeeId . '.' . $clockOutFile->getClientOriginalExtension();
            $clockOutPath = $clockOutFile->storeAs('attendance', $clockOutFileName, 'public');

            // Update the attendance record
            $attendance->update([
                'clock_out' => $clockOutTime,
                'clock_out_status' => $clockOutStatus,
                'clock_out_image' => $clockOutPath,
            ]);

            // Check employee location against office locations
            $locationData = $this->checkEmployeeLocation($employeeId, $request->latitude, $request->longitude);

            // Add entry to the attendatelocations table
            $attendanceLocation = new Attendatelocation([
                'attendance_id' => $attendance->id,
                'lat' => $request->latitude ?? '',
                'long' => $request->longitude ?? '',
                'status' => $locationData['status'],
                'attendance_type' => 'out'
            ]);

            $attendanceLocation->save();

            // Prepare reasons if early or offsite
            $noteReasons = [];
            if ($isEarly) {
                $noteReasons[] = 'Pulang lebih awal';
            }
            if ($locationData['isOffsite']) {
                $noteReasons[] = 'Di luar lokasi kantor';
            }

            // Calculate work duration
            $clockIn = Carbon::parse($attendance->created_at->format('Y-m-d') . ' ' . $attendance->clock_in);
            $clockOut = Carbon::parse($attendance->created_at->format('Y-m-d') . ' ' . $clockOutTime);
            $duration = $clockOut->diffInMinutes($clockIn);
            $hours = floor($duration / 60);
            $minutes = $duration % 60;
            $workDuration = $hours . ' jam ' . $minutes . ' menit';

            // Log the activity
            activity()
                ->causedBy(Auth::check() ? Auth::user() : null)
                ->performedOn($attendance)
                ->event('created')
                ->withProperties($attendance->toArray())
                ->log("Clock Out Berhasil");

            return response()->json([
                'success' => true,
                'message' => 'Clock Out berhasil',
                'data' => [
                    'attendance_id' => $attendance->id,
                    'employee_id' => $attendance->employee_id,
                    'clock_in_time' => $attendance->clock_in,
                    'clock_out_time' => $attendance->clock_out,
                    'clock_out_status' => $attendance->clock_out_status,
                    'location_status' => $locationData['status'],
                    'work_duration' => $workDuration,
                    'require_notes' => ($isEarly || $locationData['isOffsite']),
                    'note_reasons' => !empty($noteReasons) ? $noteReasons : null,
                    'work_schedule' => $workSchedule ? [
                        'id' => $workSchedule->id,
                        'shift_name' => $workSchedule->shift->name ?? null,
                        'shift_start' => $workSchedule->shift->shift_start ?? null,
                        'shift_end' => $workSchedule->shift->shift_end ?? null,
                    ] : null
                ]
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            Log::error('AttendanceApiController@clockOut: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan clock out: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check employee location against office locations
     * 
     * @param int $employeeId
     * @param float $latitude
     * @param float $longitude
     * @return array Location status information
     */
    private function checkEmployeeLocation($employeeId, $latitude, $longitude)
    {
        $locationStatus = 'offsite'; // Default to offsite if not within office radius
        $isOffsite = true;

        // Get employee data to get company_id
        $employee = Employee::find($employeeId);

        if ($employee && $employee->company_id) {
            // Get all offices from the employee's company
            $offices = Office::where('company_id', $employee->company_id)->get();

            foreach ($offices as $office) {
                // Calculate distance between employee location and office
                $distance = $this->calculateDistance(
                    $latitude,
                    $longitude,
                    $office->lat,
                    $office->long
                );

                // If employee is within office radius
                if ($distance <= $office->radius) {
                    $locationStatus = 'normal';
                    $isOffsite = false;
                    break; // Exit loop if already found within radius
                }
            }
        }

        return [
            'status' => $locationStatus,
            'isOffsite' => $isOffsite
        ];
    }

    /**
     * Menghitung jarak antara dua titik koordinat menggunakan rumus Haversine
     * 
     * @param float $lat1 Latitude titik pertama
     * @param float $lon1 Longitude titik pertama
     * @param float $lat2 Latitude titik kedua
     * @param float $lon2 Longitude titik kedua
     * @return float Jarak dalam meter
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        if (!$lat1 || !$lon1 || !$lat2 || !$lon2) {
            return PHP_FLOAT_MAX; // Nilai maksimum float jika koordinat tidak lengkap
        }

        // Konversi derajat ke radian
        $lat1 = deg2rad((float) $lat1);
        $lon1 = deg2rad((float) $lon1);
        $lat2 = deg2rad((float) $lat2);
        $lon2 = deg2rad((float) $lon2);

        // Radius bumi dalam meter
        $radius = 6371000;

        // Rumus Haversine
        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;
        $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlon / 2) * sin($dlon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $radius * $c;

        return $distance; // Jarak dalam meter
    }


    /**
     * Add notes to an existing attendance record
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function addAttendanceNotes(Request $request, $id)
    {
        $request->validate([
            'notes' => 'required|string',
            'attendance_type' => 'required|in:in,out',
        ]);

        try {
            $user = Auth::user();
            $attendance = Attendance::findOrFail($id);

            // Check if the user has permission to add notes to this attendance
            if (!$user->hasRole('Admin') && $user->employee_id != $attendance->employee_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak diizinkan untuk menambahkan catatan pada kehadiran ini.'
                ], 403);
            }

            // Check if notes already exist for this attendance and type
            $existingNote = AttendanceNotes::where('attendance_id', $id)
                ->where('attendance_type', $request->attendance_type)
                ->first();

            if ($existingNote) {
                // Update existing note
                $existingNote->update([
                    'notes' => $request->notes
                ]);
                $message = 'Catatan kehadiran berhasil diperbarui.';
                $note = $existingNote;
            } else {
                // Create new note
                $note = new AttendanceNotes([
                    'attendance_id' => $id, // Fixed: Use the $id parameter instead of $request->attendance_id
                    'notes' => $request->notes,
                    'attendance_type' => $request->attendance_type
                ]);
                $note->save();
                $message = 'Catatan kehadiran berhasil ditambahkan.';
            }

            // Log the activity
            activity()
                ->causedBy($user)
                ->performedOn($note)  // Fixed: Use the note object instead of the attendance
                ->event('created')
                ->withProperties($note->toArray())
                ->log("Menambahkan catatan kehadiran");

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $note
            ]);
        } catch (Exception $e) {
            Log::error('AttendanceApiController@addAttendanceNotes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan catatan kehadiran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get attendance notes for an attendance record
     * 
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAttendanceNotes($id, Request $request)
    {
        $request->validate([
            'attendance_type' => 'required|in:in,out',
        ]);

        try {
            $user = Auth::user();
            $attendance = Attendance::findOrFail($id);

            // Check if the user has permission to view notes for this attendance
            if (!$user->hasRole('Admin') && $user->employee_id != $attendance->employee_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak diizinkan untuk melihat catatan pada kehadiran ini.'
                ], 403);
            }

            $notes = AttendanceNotes::where('attendance_id', $id)
                ->where('attendance_type', $request->attendance_type)
                ->first();

            return response()->json([
                'success' => true,
                'data' => $notes ?? null,
                'message' => $notes ? 'Catatan kehadiran ditemukan.' : 'Tidak ada catatan kehadiran.'
            ]);
        } catch (Exception $e) {
            Log::error('AttendanceApiController@getAttendanceNotes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan catatan kehadiran: ' . $e->getMessage()
            ], 500);
        }
    }
}