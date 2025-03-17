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
            'notes' => 'nullable|string',
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

            // Jika work_schedule_id tidak diberikan, cari jadwal kerja untuk karyawan hari ini
            if (!$workScheduleId) {
                $workSchedule = WorkSchedule::with('shift')
                    ->where('employee_id', $employeeId)
                    ->where('schedule_date', $currentTime->format('Y-m-d'))
                    ->first();

                if ($workSchedule) {
                    $workScheduleId = $workSchedule->id;
                }
            } else {
                // Jika work_schedule_id diberikan, ambil data work schedule
                $workSchedule = WorkSchedule::with('shift')->find($workScheduleId);

                // Periksa apakah work schedule ini milik karyawan yang sedang clock in
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
                $shiftStartTime = Carbon::parse($shift->shift_start);

                // Format waktu untuk perbandingan (hanya jam:menit:detik)
                $formattedShiftStart = $shiftStartTime->format('H:i:s');
                $formattedClockIn = $clockInTime;

                // Periksa apakah clock in telat
                if ($currentTime->gt($shiftStartTime)) {
                    $clockInStatus = 'late';
                    $isLate = true;
                }

                // Hitung selisih waktu antara shift start dan clock in
                $diffInMinutes = $currentTime->diffInMinutes($shiftStartTime);

                // Pesan pencocokan shift
                if ($formattedClockIn === $formattedShiftStart) {
                    $shiftMatchMessage = 'Clock in tepat waktu sesuai jadwal shift';
                } elseif ($isLate) {
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

            // Cek lokasi karyawan dengan lokasi kantor
            $locationStatus = 'offsite'; // Default ke offsite jika tidak dalam radius kantor
            $isOffsite = true;

            // Dapatkan data employee untuk mendapatkan company_id
            $employee = Employee::find($employeeId);

            if ($employee && $employee->company_id) {
                // Dapatkan semua kantor dari perusahaan karyawan
                $offices = Office::where('company_id', $employee->company_id)->get();

                foreach ($offices as $office) {
                    // Hitung jarak antara lokasi karyawan dan kantor
                    $distance = $this->calculateDistance(
                        $request->latitude,
                        $request->longitude,
                        $office->lat,
                        $office->long
                    );

                    // Jika karyawan berada dalam radius kantor
                    if ($distance <= $office->radius) {
                        $locationStatus = 'normal';
                        $isOffsite = false;
                        break; // Keluar dari loop jika sudah ditemukan dalam radius
                    }
                }
            }

            // Tambahkan entri ke tabel attendatelocations
            $attendanceLocation = new Attendatelocation([
                'attendance_id' => $attendance->id,
                'lat' => $request->latitude ?? '',
                'long' => $request->longitude ?? '',
                'status' => $locationStatus,
                'attendance_type' => 'in'
            ]);

            $attendanceLocation->save();

            // Tambahkan catatan jika telat atau offsite
            if ($isLate || $isOffsite) {
                $noteReason = [];

                if ($isLate) {
                    $noteReason[] = 'Telat';
                }

                if ($isOffsite) {
                    $noteReason[] = 'Di luar lokasi kantor';
                }

                $defaultNote = 'Karyawan ' . implode(' dan ', $noteReason);
                $notes = $request->notes ?? $defaultNote;

                $attendanceNote = new AttendanceNotes([
                    'attendance_id' => $attendance->id,
                    'notes' => $notes,
                    'attendance_type' => 'in'
                ]);

                $attendanceNote->save();
            }

            // Log the activity
            activity()
                ->causedBy(Auth::check() ? Auth::user() : null)
                ->performedOn($attendance)
                ->event('clock_in')
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
                    'location_status' => $locationStatus,
                    'shift_match' => $shiftMatchMessage,
                    'require_notes' => ($isLate || $isOffsite),
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
            'notes' => 'nullable|string', // Tambahkan validasi untuk notes
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

            // Calculate if the clock out is early based on the work schedule
            $clockOutStatus = 'normal';
            $workSchedule = null;
            $isEarly = false;
            $clockOutTime = null;

            if ($attendance->work_schedule_id) {
                $workSchedule = WorkSchedule::with('shift')->find($attendance->work_schedule_id);
                if ($workSchedule && $workSchedule->shift) {
                    // Always use the shift end time for clock out
                    $clockOutTime = $workSchedule->shift->shift_end;
                    $shiftEndTime = Carbon::parse($workSchedule->shift->shift_end);
                    $currentTime = Carbon::now();

                    if ($currentTime->lt($shiftEndTime)) {
                        $clockOutStatus = 'early';
                        $isEarly = true;
                    }
                }
            }

            // If no shift schedule is found, use current time
            if (!$clockOutTime) {
                $clockOutTime = Carbon::now()->format('H:i:s');
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

            // Cek lokasi karyawan dengan lokasi kantor
            $locationStatus = 'offsite'; // Default ke offsite jika tidak dalam radius kantor
            $isOffsite = true;

            // Dapatkan data employee untuk mendapatkan company_id
            $employee = Employee::find($employeeId);

            if ($employee && $employee->company_id) {
                // Dapatkan semua kantor dari perusahaan karyawan
                $offices = Office::where('company_id', $employee->company_id)->get();

                foreach ($offices as $office) {
                    // Hitung jarak antara lokasi karyawan dan kantor
                    $distance = $this->calculateDistance(
                        $request->latitude,
                        $request->longitude,
                        $office->lat,
                        $office->long
                    );

                    // Jika karyawan berada dalam radius kantor
                    if ($distance <= $office->radius) {
                        $locationStatus = 'normal';
                        $isOffsite = false;
                        break; // Keluar dari loop jika sudah ditemukan dalam radius
                    }
                }
            }

            // Tambahkan entri ke tabel attendatelocations
            $attendanceLocation = new Attendatelocation([
                'attendance_id' => $attendance->id,
                'lat' => $request->latitude ?? '',
                'long' => $request->longitude ?? '',
                'status' => $locationStatus,
                'attendance_type' => 'out'
            ]);

            $attendanceLocation->save();

            // Tambahkan catatan jika pulang awal atau offsite
            if ($isEarly || $isOffsite) {
                $noteReason = [];

                if ($isEarly) {
                    $noteReason[] = 'Pulang lebih awal';
                }

                if ($isOffsite) {
                    $noteReason[] = 'Di luar lokasi kantor';
                }

                $defaultNote = 'Karyawan ' . implode(' dan ', $noteReason);
                $notes = $request->notes ?? $defaultNote;

                $attendanceNote = new AttendanceNotes([
                    'attendance_id' => $attendance->id,
                    'notes' => $notes,
                    'attendance_type' => 'out'
                ]);

                $attendanceNote->save();
            }

            // Calculate work duration
            $clockIn = Carbon::parse($attendance->created_at->format('Y-m-d') . ' ' . $attendance->clock_in);
            $clockOut = Carbon::parse($attendance->created_at->format('Y-m-d') . ' ' . $attendance->clock_out);
            $duration = $clockOut->diffInMinutes($clockIn);
            $hours = floor($duration / 60);
            $minutes = $duration % 60;
            $workDuration = $hours . ' jam ' . $minutes . ' menit';

            // Log the activity
            activity()
                ->causedBy(Auth::check() ? Auth::user() : null)
                ->performedOn($attendance)
                ->event('clock_out')
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
                    'location_status' => $locationStatus,
                    'work_duration' => $workDuration,
                    'require_notes' => ($isEarly || $isOffsite),
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
     * Get attendance status for an employee
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatus(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user->employee_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terkait dengan karyawan manapun.'
                ], 400);
            }

            $employeeId = $user->employee_id;

            $employee = Employee::findOrFail($employeeId);
            $today = Carbon::now()->format('Y-m-d');

            // Get today's work schedule
            $workSchedule = WorkSchedule::with('shift')
                ->where('employee_id', $employeeId)
                ->where('schedule_date', $today)
                ->first();

            // Get today's attendance
            $attendance = Attendance::where('employee_id', $employeeId)
                ->whereDate('created_at', $today)
                ->first();

            $status = [
                'employee' => [
                    'id' => $employee->id,
                    'name' => $employee->name,
                ],
                'date' => Carbon::now()->format('d F Y'),
                'current_time' => Carbon::now()->format('H:i:s'),
                'has_clocked_in' => $attendance ? true : false,
                'has_clocked_out' => $attendance && $attendance->clock_out ? true : false,
                'attendance' => $attendance ? [
                    'id' => $attendance->id,
                    'clock_in' => $attendance->clock_in,
                    'clock_in_status' => $attendance->clock_in_status,
                    'clock_out' => $attendance->clock_out,
                    'clock_out_status' => $attendance->clock_out_status,
                ] : null,
                'work_schedule' => $workSchedule ? [
                    'id' => $workSchedule->id,
                    'shift_name' => $workSchedule->shift->name ?? null,
                    'shift_start' => $workSchedule->shift->shift_start ?? null,
                    'shift_end' => $workSchedule->shift->shift_end ?? null,
                ] : null
            ];

            return response()->json([
                'success' => true,
                'data' => $status
            ]);
        } catch (Exception $e) {
            Log::error('AttendanceApiController@getStatus: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan status absensi: ' . $e->getMessage()
            ], 500);
        }
    }
}