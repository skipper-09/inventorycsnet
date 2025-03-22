<?php

namespace App\Http\Controllers\Master;

use App\Exports\WorkScheduleExport;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\EmployeHoliday;
use App\Models\WorkSchedule;
use App\Models\Shift;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class WorkScheduleController extends Controller
{
    public function index()
    {
        $data = [
            "title" => "Jadwal Kerja Karyawan",
            "employees" => Employee::all(),
            "Departement" => Department::select('id', 'name')->whereHas('employees')->get(),
            'shift' => Shift::all(),
        ];

        return view("pages.master.workschedule.index", $data);
    }

    public function getShifts()
    {
        // Fetch all shifts
        $shifts = Shift::all();
        return response()->json($shifts);
    }

    public function getEmployeeEvents($employeeId, Request $request)
    {
        $events = WorkSchedule::with('shift')
            ->where('employee_id', $employeeId)
            ->whereBetween('schedule_date', [$request->start, $request->end])
            ->get();

        $formattedEvents = $events->map(function ($event) {
            $shiftStart = optional($event->shift)->shift_start;
            $shiftEnd = optional($event->shift)->shift_end;
            $shiftName = optional($event->shift)->name;

            return [
                'title' => $event->status == 'offday' ? 'offday' : 'Jadwal Kerja' . ' (' . $shiftName . ')',
                'start' => Carbon::parse($event->schedule_date)->setTimeFrom(Carbon::parse($shiftStart))->toIso8601String(),
                'end' => Carbon::parse($event->schedule_date)->setTimeFrom(Carbon::parse($shiftEnd))->toIso8601String(),
                'status' => $event->status == 'offday' ? 'offday' : 'work',
                'shift_name' => $shiftName,  // Include shift name
                'shift_start' => Carbon::parse($shiftStart)->toIso8601String(),
                'shift_end' => Carbon::parse($shiftEnd)->toIso8601String(),
            ];
        });

        return response()->json($formattedEvents);
    }

    public function createSchedule(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'shift_id' => 'required_if:status,work|exists:shifts,id',
            'status' => 'required|in:work,offday',
        ]);

        $shiftId = (int) $validated['shift_id'];
        $schedule = WorkSchedule::updateOrCreate([
            'employee_id' => $validated['employee_id'],
            'schedule_date' => Carbon::parse($validated['date']),
        ], [
            'shift_id' => $shiftId,
            'status' => $validated['status'],
        ]);

        return response()->json(['success' => true, 'schedule' => $schedule]);
    }

    // Create multiple schedules for an employee within a date range
    public function createBulkSchedule(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'shift_id' => 'required',
            'status' => 'required|in:work,offday',
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $employeeId = $validated['employee_id'];
        $status = $validated['status'];
        $shift_id = $validated['shift_id'];

        // Create schedules for the selected date range
        $schedules = [];
        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            $schedules[] = WorkSchedule::updateOrCreate([
                'employee_id' => $employeeId,
                'schedule_date' => $date,
            ], [
                'status' => $status,
                'shift_id' => $shift_id,
            ]);
        }

        return response()->json(['success' => true, 'schedules' => $schedules]);
    }


    //create offdays
    public function createOffday(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'status' => 'required|in:work,offday',
        ]);

        DB::beginTransaction();
        try {
            $schedule = WorkSchedule::updateOrCreate([
                'employee_id' => $validated['employee_id'],
                'schedule_date' => Carbon::parse($validated['date']),
            ], [
                'shift_id' => null,
                'status' => $validated['status'],
            ]);

            DB::commit();
            return response()->json(['success' => true, 'schedule' => $schedule]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'status' => "Gagal",
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }

    }

    public function createBulkOffday(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'status' => 'required|in:work,offday',
        ]);

        DB::beginTransaction();
        try {
            $startDate = Carbon::parse($validated['start_date']);
            $endDate = Carbon::parse($validated['end_date']);
            $employeeId = $validated['employee_id'];
            $status = $validated['status'];

            $schedules = [];
            for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                $schedules[] = WorkSchedule::updateOrCreate([
                    'employee_id' => $employeeId,
                    'schedule_date' => $date,
                ], [
                    'status' => $status,
                    'shift_id' => null,
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'schedules' => $schedules]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'status' => "Gagal",
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    public function createGroupSchedule(Request $request)
    {
        $dateRange = $request->date;
        $dates = explode(' - ', $dateRange);
        $startDate = $dates[0];
        $endDate = $dates[1];


        $request->validate([
            'departement_id' => 'required|exists:departments,id',
            'date' => 'required',
            'shift_id' => 'required_if:status,work|exists:shifts,id',
        ], [
            'departement_id.required' => 'Departemen harus dipilih.',
            'departement_id.exists' => 'Departemen yang dipilih tidak valid.',
            'date.required' => 'Tanggal harus diisi.',
            'shift_id.required_if' => 'Shift harus dipilih jika statusnya adalah "work".',
            'shift_id.exists' => 'Shift yang dipilih tidak valid.',
        ]);

        $employee = Employee::where('department_id', $request->departement_id)->get();

        foreach ($employee as $item) {
            for ($date = Carbon::parse($startDate); $date->lte(Carbon::parse($endDate)); $date->addDay()) {
                WorkSchedule::updateOrCreate([
                    'employee_id' => $item->id,
                    'schedule_date' => $date,
                ], [
                    'status' => 'work',
                    'shift_id' => $request->shift_id,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'status' => "Berhasil",
            'message' => 'Group Jadwal Berhasil dibuat.'
        ]);
    }

    public function setWeekdayOffdaysForMonth(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'year' => 'required|integer',
            'month' => 'required|integer|between:1,12',
            'weekday' => 'required|integer|between:0,6', // 0 = Minggu, 1 = Senin, ..., 6 = Sabtu
        ]);

        DB::beginTransaction();
        try {
            $employeeId = $validated['employee_id'];
            $year = $validated['year'];
            $month = $validated['month'];
            $weekday = (int) $validated['weekday']; // Konversi ke integer

            // Mapping weekday number to name (for response message)
            $weekdayNames = [
                0 => 'Minggu',
                1 => 'Senin',
                2 => 'Selasa',
                3 => 'Rabu',
                4 => 'Kamis',
                5 => 'Jumat',
                6 => 'Sabtu'
            ];

            // Get the first day of the month
            $date = Carbon::createFromDate($year, $month, 1);

            // Get the last day of the month
            $lastDay = $date->copy()->endOfMonth();

            $targetDates = [];

            // Loop through the month and find all days matching the requested weekday
            for ($currentDate = $date; $currentDate->lte($lastDay); $currentDate->addDay()) {
                Log::info('Processing date:', ['date' => $currentDate->format('Y-m-d'), 'dayOfWeek' => $currentDate->dayOfWeek]);

                if ($currentDate->dayOfWeek === $weekday) { // Sekarang $weekday adalah integer
                    $formattedDate = $currentDate->format('Y-m-d');
                    $targetDates[] = $formattedDate;

                    WorkSchedule::updateOrCreate([
                        'employee_id' => $employeeId,
                        'schedule_date' => $formattedDate,
                    ], [
                        'status' => 'offday',
                        'shift_id' => null,
                    ]);
                }
            }

            // Jika tidak ada tanggal yang sesuai
            if (empty($targetDates)) {
                Log::info('No matching dates found for weekday:', ['weekday' => $weekday]);
                DB::commit();
                return response()->json([
                    'success' => true,
                    'status' => 'Berhasil',
                    'message' => 'Tidak ada hari ' . $weekdayNames[$weekday] . ' di bulan ini.',
                    'target_dates' => []
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'status' => 'Berhasil',
                'message' => 'Jadwal libur untuk hari ' . $weekdayNames[$weekday] . ' berhasil dibuat',
                'target_dates' => $targetDates
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to set weekday offdays: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'status' => 'Gagal',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteSchedule(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $employeeId = $validated['employee_id'];
            $date = Carbon::parse($validated['date']);

            // Find and delete the schedule
            $deleted = WorkSchedule::where('employee_id', $employeeId)
                ->where('schedule_date', $date)
                ->delete();

            DB::commit();
            return response()->json([
                'success' => true,
                'status' => 'Berhasil',
                'message' => 'Jadwal berhasil dihapus'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete schedule: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'status' => 'Gagal',
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteBulkSchedule(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $employeeId = $validated['employee_id'];
            $startDate = Carbon::parse($validated['start_date']);
            $endDate = Carbon::parse($validated['end_date']);

            // Delete schedules in the date range
            $deleted = WorkSchedule::where('employee_id', $employeeId)
                ->whereBetween('schedule_date', [$startDate, $endDate])
                ->delete();

            DB::commit();
            return response()->json([
                'success' => true,
                'status' => 'Berhasil',
                'message' => 'Jadwal berhasil dihapus'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete bulk schedules: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'status' => 'Gagal',
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportSchedule($employeeId, $format, Request $request)
    {
        // Validate request
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        // Get employee details
        $employee = Employee::findOrFail($employeeId);

        // Get work schedules for the employee within the date range
        $schedules = WorkSchedule::with('shift')
            ->where('employee_id', $employeeId)
            ->whereBetween('schedule_date', [$startDate, $endDate])
            ->orderBy('schedule_date')
            ->get();

        $fileName = "jadwal_kerja_{$employee->name}_{$startDate->format('Ymd')}_{$endDate->format('Ymd')}.xlsx";

        // return (new WorkScheduleExport($schedules, $employee, $startDate, $endDate))
        //     ->download($fileName);
        
        return Excel::download(new WorkScheduleExport($schedules, $employee, $startDate, $endDate), $fileName);
    }

    // public function getData()
    // {
    //     $data = WorkSchedule::with('employee', 'shift')
    //         ->orderByDesc('schedule_date')
    //         ->get();

    //     return DataTables::of($data)
    //         ->addIndexColumn()
    //         ->addColumn('employee_name', function ($data) {
    //             return $data->employee->name ?? 'N/A';
    //         })
    //         ->addColumn('shift_name', function ($data) {
    //             return $data->shift->name ?? 'N/A';
    //         })
    //         ->addColumn('schedule_date_formatted', function ($data) {
    //             return Carbon::parse($data->schedule_date)->format('d F Y');
    //         })
    //         ->addColumn('is_offday', function ($data) {
    //             $isOffday = EmployeHoliday::where('employee_id', $data->employee_id)
    //                 ->where('day_off', $data->schedule_date)
    //                 ->exists();

    //             return $isOffday ? '<span class="badge bg-warning">Hari Libur</span>' : '<span class="badge bg-success">Hari Kerja</span>';
    //         })
    //         ->addColumn('action', function ($data) {
    //             $userauth = Auth::user();
    //             $button = '';

    //             // Tampilkan tombol edit jika user memiliki akses
    //             if ($userauth->can('update-workschedule')) {
    //                 $button .= ' <button class="btn btn-sm btn-success btn-edit" data-id="' . $data->id . '" data-route="' . route('workschedule.edit', ['id' => $data->id]) . '" title="Edit Jadwal Kerja"><i class="fas fa-pen"></i></button>';
    //             }

    //             // Tampilkan tombol hapus jika user memiliki akses
    //             if ($userauth->can('delete-workschedule')) {
    //                 $button .= ' <button class="btn btn-sm btn-danger btn-delete" data-id="' . $data->id . '" data-route="' . route('workschedule.delete', ['id' => $data->id]) . '" title="Hapus Jadwal Kerja"><i class="fas fa-trash"></i></button>';
    //             }

    //             return '<div class="d-flex gap-2">' . $button . '</div>';
    //         })
    //         ->rawColumns(['action', 'is_offday', 'employee_name', 'shift_name', 'schedule_date_formatted', 'is_offday'])
    //         ->make(true);
    // }

    // public function create()
    // {
    //     $data = [
    //         'title' => 'Tambah Jadwal Kerja Karyawan',
    //         'employees' => Employee::orderBy('name')->get(),
    //         'shifts' => Shift::orderBy('name')->get(),
    //     ];

    //     return view('pages.master.workschedule.add', $data);
    // }

    // public function store(Request $request)
    // {
    //     try {
    //         $validated = $request->validate([
    //             'employee_id' => 'required|exists:employees,id',
    //             'shift_id' => 'required|exists:shifts,id',
    //             'schedule_date' => 'required|date',
    //             'is_offday' => 'sometimes|boolean',
    //         ]);

    //         // Check if schedule for this employee on this date already exists
    //         $existingSchedule = WorkSchedule::where('employee_id', $request->employee_id)
    //             ->where('schedule_date', $request->schedule_date)
    //             ->first();

    //         if ($existingSchedule) {
    //             throw ValidationException::withMessages([
    //                 'schedule_date' => 'Jadwal untuk karyawan ini pada tanggal tersebut sudah ada.'
    //             ]);
    //         }

    //         $employee = Employee::findOrFail($request->employee_id);
    //         $shift = Shift::findOrFail($request->shift_id);

    //         // Check if this is an off day request
    //         $isOffday = $request->has('is_offday') && $request->is_offday == 1;

    //         if ($isOffday) {
    //             // Check number of holidays in the month
    //             $month = Carbon::parse($request->schedule_date)->month;
    //             $year = Carbon::parse($request->schedule_date)->year;

    //             $holidaysCount = EmployeHoliday::where('employee_id', $employee->id)
    //                 ->whereMonth('day_off', $month)
    //                 ->whereYear('day_off', $year)
    //                 ->count();

    //             if ($holidaysCount >= 4) {
    //                 throw ValidationException::withMessages([
    //                     'is_offday' => 'Karyawan sudah mencapai batas hari libur (4 kali) dalam bulan ini.'
    //                 ]);
    //             }
    //         }

    //         // Create work schedule
    //         $workSchedule = WorkSchedule::create([
    //             'employee_id' => $employee->id,
    //             'shift_id' => $shift->id,
    //             'schedule_date' => $request->schedule_date,
    //         ]);

    //         // If it's an off day, create holiday record
    //         if ($isOffday) {
    //             EmployeHoliday::create([
    //                 'employee_id' => $employee->id,
    //                 'day_off' => $request->schedule_date,
    //             ]);
    //         }

    //         activity()
    //             ->causedBy(Auth::user())
    //             ->event('created')
    //             ->withProperties($workSchedule->toArray())
    //             ->log("Jadwal Kerja Baru Ditambahkan");

    //         return redirect()->route('workschedule.index')->with([
    //             'status' => 'Success!',
    //             'message' => 'Berhasil Menambahkan Jadwal Kerja!'
    //         ]);
    //     } catch (ValidationException $e) {
    //         return redirect()->back()->withErrors($e->errors())->withInput();
    //     } catch (Exception $e) {
    //         Log::error('WorkScheduleController@store: ' . $e->getMessage());
    //         return redirect()->back()->with([
    //             'status' => 'Error!', 
    //             'message' => 'Gagal Menambahkan Jadwal Kerja: ' . $e->getMessage()
    //         ])->withInput();
    //     }
    // }

    // public function edit($id)
    // {
    //     try {
    //         $workSchedule = WorkSchedule::with('employee', 'shift')->findOrFail($id);

    //         // Check if this date is a holiday for this employee
    //         $isOffday = EmployeHoliday::where('employee_id', $workSchedule->employee_id)
    //             ->where('day_off', $workSchedule->schedule_date)
    //             ->exists();

    //         $data = [
    //             'title' => 'Edit Jadwal Kerja Karyawan',
    //             'workSchedule' => $workSchedule,
    //             'employees' => Employee::orderBy('name')->get(),
    //             'shifts' => Shift::orderBy('name')->get(),
    //             'isOffday' => $isOffday,
    //         ];

    //         return view('pages.master.workschedule.edit', $data);
    //     } catch (Exception $e) {
    //         Log::error('WorkScheduleController@edit: ' . $e->getMessage());
    //         return redirect()->route('workschedule.index')->with([
    //             'status' => 'Error!', 
    //             'message' => 'Jadwal Kerja tidak ditemukan'
    //         ]);
    //     }
    // }

    // public function update(Request $request, $id)
    // {
    //     try {
    //         $validated = $request->validate([
    //             'employee_id' => 'required|exists:employees,id',
    //             'shift_id' => 'required|exists:shifts,id',
    //             'schedule_date' => 'required|date',
    //             'is_offday' => 'sometimes|boolean',
    //         ]);

    //         $workSchedule = WorkSchedule::findOrFail($id);
    //         $oldSchedule = $workSchedule->toArray();
    //         $dateChanged = $workSchedule->schedule_date != $request->schedule_date || 
    //                       $workSchedule->employee_id != $request->employee_id;

    //         // Check for duplicate if date or employee changed
    //         if ($dateChanged) {
    //             $existingSchedule = WorkSchedule::where('employee_id', $request->employee_id)
    //                 ->where('schedule_date', $request->schedule_date)
    //                 ->where('id', '!=', $id)
    //                 ->first();

    //             if ($existingSchedule) {
    //                 throw ValidationException::withMessages([
    //                     'schedule_date' => 'Jadwal untuk karyawan ini pada tanggal tersebut sudah ada.'
    //                 ]);
    //             }
    //         }

    //         // Update the schedule
    //         $workSchedule->update([
    //             'employee_id' => $request->employee_id,
    //             'shift_id' => $request->shift_id,
    //             'schedule_date' => $request->schedule_date,
    //         ]);

    //         // Handle off day status
    //         $isOffday = $request->has('is_offday') && $request->is_offday == 1;
    //         $existingHoliday = EmployeHoliday::where('employee_id', $workSchedule->employee_id)
    //             ->where('day_off', $oldSchedule['schedule_date'])
    //             ->first();

    //         // If it was a holiday before but not now, or date changed
    //         if ($existingHoliday) {
    //             if (!$isOffday) {
    //                 // Remove holiday
    //                 $existingHoliday->delete();
    //             } elseif ($dateChanged) {
    //                 // Update holiday date
    //                 $existingHoliday->update(['day_off' => $request->schedule_date]);
    //             }
    //         } 
    //         // Wasn't a holiday before but is now
    //         elseif ($isOffday) {
    //             // Check holiday count first
    //             $month = Carbon::parse($request->schedule_date)->month;
    //             $year = Carbon::parse($request->schedule_date)->year;

    //             $holidaysCount = EmployeHoliday::where('employee_id', $request->employee_id)
    //                 ->whereMonth('day_off', $month)
    //                 ->whereYear('day_off', $year)
    //                 ->count();

    //             if ($holidaysCount >= 4) {
    //                 throw ValidationException::withMessages([
    //                     'is_offday' => 'Karyawan sudah mencapai batas hari libur (4 kali) dalam bulan ini.'
    //                 ]);
    //             }

    //             // Create new holiday
    //             EmployeHoliday::create([
    //                 'employee_id' => $request->employee_id,
    //                 'day_off' => $request->schedule_date,
    //             ]);
    //         }

    //         $workSchedule->refresh();

    //         activity()
    //             ->causedBy(Auth::user())
    //             ->event('updated')
    //             ->withProperties([
    //                 'old' => $oldSchedule,
    //                 'new' => $workSchedule->toArray()
    //             ])
    //             ->log("Jadwal Kerja Berhasil Diperbarui");

    //         return redirect()->route('workschedule.index')->with([
    //             'status' => 'Success!',
    //             'message' => 'Berhasil Mengupdate Jadwal Kerja!'
    //         ]);
    //     } catch (ValidationException $e) {
    //         return redirect()->back()->withErrors($e->errors())->withInput();
    //     } catch (Exception $e) {
    //         Log::error('WorkScheduleController@update: ' . $e->getMessage());
    //         return redirect()->back()->with([
    //             'status' => 'Error!',
    //             'message' => 'Gagal Mengupdate Jadwal Kerja: ' . $e->getMessage()
    //         ])->withInput();
    //     }
    // }

    // public function destroy($id)
    // {
    //     try {
    //         $workSchedule = WorkSchedule::findOrFail($id);

    //         // Check if this is a holiday and delete the holiday record if it exists
    //         $holiday = EmployeHoliday::where('employee_id', $workSchedule->employee_id)
    //             ->where('day_off', $workSchedule->schedule_date)
    //             ->first();

    //         if ($holiday) {
    //             $holiday->delete();
    //         }

    //         activity()
    //             ->causedBy(Auth::user())
    //             ->event('deleted')
    //             ->withProperties($workSchedule->toArray())
    //             ->log("Jadwal Kerja Berhasil Dihapus");

    //         $workSchedule->delete();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Jadwal Kerja Berhasil Dihapus!',
    //         ]);
    //     } catch (Exception $e) {
    //         Log::error('WorkScheduleController@destroy: ' . $e->getMessage());
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Gagal menghapus jadwal kerja: ' . $e->getMessage(),
    //         ]);
    //     }
    // }
}