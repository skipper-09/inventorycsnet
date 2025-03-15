<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
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

            // Get the work schedule for the employee if not provided
            $workScheduleId = $request->work_schedule_id;
            if (!$workScheduleId) {
                $workSchedule = WorkSchedule::with('shift')
                    ->where('employee_id', $employeeId)
                    ->where('schedule_date', Carbon::now()->format('Y-m-d'))
                    ->first();

                if ($workSchedule) {
                    $workScheduleId = $workSchedule->id;
                }
            }

            // Calculate if the clock in is late based on the work schedule
            $clockInStatus = 'normal';
            $workSchedule = null;

            if ($workScheduleId) {
                $workSchedule = WorkSchedule::with('shift')->find($workScheduleId);
                if ($workSchedule && $workSchedule->shift) {
                    $shiftStartTime = Carbon::parse($workSchedule->shift->shift_start);
                    $currentTime = Carbon::now();

                    if ($currentTime->gt($shiftStartTime)) {
                        $clockInStatus = 'late';
                    }
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
                'clock_in' => Carbon::now()->format('H:i:s'),
                'clock_in_status' => $clockInStatus,
                'clock_in_image' => $clockInPath,
                'latitude_in' => $request->latitude,
                'longitude_in' => $request->longitude,
            ]);

            $attendance->save();

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

            // Calculate if the clock out is early based on the work schedule
            $clockOutStatus = 'normal';
            $workSchedule = null;

            if ($attendance->work_schedule_id) {
                $workSchedule = WorkSchedule::with('shift')->find($attendance->work_schedule_id);
                if ($workSchedule && $workSchedule->shift) {
                    $shiftEndTime = Carbon::parse($workSchedule->shift->shift_end);
                    $currentTime = Carbon::now();

                    if ($currentTime->lt($shiftEndTime)) {
                        $clockOutStatus = 'early';
                    }
                }
            }

            // Process the clock out image
            $clockOutFile = $request->file('clock_out_image');
            $clockOutFileName = time() . '_out_' . $employeeId . '.' . $clockOutFile->getClientOriginalExtension();
            $clockOutPath = $clockOutFile->storeAs('attendance', $clockOutFileName, 'public');

            // Update the attendance record
            $attendance->update([
                'clock_out' => Carbon::now()->format('H:i:s'),
                'clock_out_status' => $clockOutStatus,
                'clock_out_image' => $clockOutPath,
                'latitude_out' => $request->latitude,
                'longitude_out' => $request->longitude,
            ]);

            // Calculate work duration
            // $clockIn = Carbon::parse($attendance->created_at->format('Y-m-d') . ' ' . $attendance->clock_in);
            // $clockOut = Carbon::parse($attendance->created_at->format('Y-m-d') . ' ' . $attendance->clock_out);
            // $duration = $clockOut->diffInMinutes($clockIn);
            // $hours = floor($duration / 60);
            // $minutes = $duration % 60;
            // $workDuration = $hours . ' jam ' . $minutes . ' menit';

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
                    // 'work_duration' => $workDuration,
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