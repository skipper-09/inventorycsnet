<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\EmployeHoliday;
use App\Models\WorkSchedule;
use App\Models\Shift;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
        ];

        return view("pages.master.workschedule.index", $data);
    }

    public function getData()
    {
        $data = WorkSchedule::with('employee', 'shift')
            ->orderByDesc('schedule_date')
            ->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('employee_name', function ($data) {
                return $data->employee->name ?? 'N/A';
            })
            ->addColumn('shift_name', function ($data) {
                return $data->shift->name ?? 'N/A';
            })
            ->addColumn('schedule_date_formatted', function ($data) {
                return Carbon::parse($data->schedule_date)->format('d F Y');
            })
            ->addColumn('is_offday', function ($data) {
                $isOffday = EmployeHoliday::where('employee_id', $data->employee_id)
                    ->where('day_off', $data->schedule_date)
                    ->exists();
                
                return $isOffday ? '<span class="badge bg-warning">Hari Libur</span>' : '<span class="badge bg-success">Hari Kerja</span>';
            })
            ->addColumn('action', function ($data) {
                $userauth = Auth::user();
                $button = '';

                // Tampilkan tombol edit jika user memiliki akses
                if ($userauth->can('update-workschedule')) {
                    $button .= ' <button class="btn btn-sm btn-success btn-edit" data-id="' . $data->id . '" data-route="' . route('workschedule.edit', ['id' => $data->id]) . '" title="Edit Jadwal Kerja"><i class="fas fa-pen"></i></button>';
                }

                // Tampilkan tombol hapus jika user memiliki akses
                if ($userauth->can('delete-workschedule')) {
                    $button .= ' <button class="btn btn-sm btn-danger btn-delete" data-id="' . $data->id . '" data-route="' . route('workschedule.delete', ['id' => $data->id]) . '" title="Hapus Jadwal Kerja"><i class="fas fa-trash"></i></button>';
                }

                return '<div class="d-flex gap-2">' . $button . '</div>';
            })
            ->rawColumns(['action', 'is_offday', 'employee_name', 'shift_name', 'schedule_date_formatted', 'is_offday'])
            ->make(true);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Jadwal Kerja Karyawan',
            'employees' => Employee::orderBy('name')->get(),
            'shifts' => Shift::orderBy('name')->get(),
        ];

        return view('pages.master.workschedule.add', $data);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'shift_id' => 'required|exists:shifts,id',
                'schedule_date' => 'required|date',
                'is_offday' => 'sometimes|boolean',
            ]);
            
            // Check if schedule for this employee on this date already exists
            $existingSchedule = WorkSchedule::where('employee_id', $request->employee_id)
                ->where('schedule_date', $request->schedule_date)
                ->first();
                
            if ($existingSchedule) {
                throw ValidationException::withMessages([
                    'schedule_date' => 'Jadwal untuk karyawan ini pada tanggal tersebut sudah ada.'
                ]);
            }
            
            $employee = Employee::findOrFail($request->employee_id);
            $shift = Shift::findOrFail($request->shift_id);

            // Check if this is an off day request
            $isOffday = $request->has('is_offday') && $request->is_offday == 1;
            
            if ($isOffday) {
                // Check number of holidays in the month
                $month = Carbon::parse($request->schedule_date)->month;
                $year = Carbon::parse($request->schedule_date)->year;
                
                $holidaysCount = EmployeHoliday::where('employee_id', $employee->id)
                    ->whereMonth('day_off', $month)
                    ->whereYear('day_off', $year)
                    ->count();
                
                if ($holidaysCount >= 4) {
                    throw ValidationException::withMessages([
                        'is_offday' => 'Karyawan sudah mencapai batas hari libur (4 kali) dalam bulan ini.'
                    ]);
                }
            }

            // Create work schedule
            $workSchedule = WorkSchedule::create([
                'employee_id' => $employee->id,
                'shift_id' => $shift->id,
                'schedule_date' => $request->schedule_date,
            ]);

            // If it's an off day, create holiday record
            if ($isOffday) {
                EmployeHoliday::create([
                    'employee_id' => $employee->id,
                    'day_off' => $request->schedule_date,
                ]);
            }

            activity()
                ->causedBy(Auth::user())
                ->event('created')
                ->withProperties($workSchedule->toArray())
                ->log("Jadwal Kerja Baru Ditambahkan");

            return redirect()->route('workschedule.index')->with([
                'status' => 'Success!',
                'message' => 'Berhasil Menambahkan Jadwal Kerja!'
            ]);
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            Log::error('WorkScheduleController@store: ' . $e->getMessage());
            return redirect()->back()->with([
                'status' => 'Error!', 
                'message' => 'Gagal Menambahkan Jadwal Kerja: ' . $e->getMessage()
            ])->withInput();
        }
    }

    public function edit($id)
    {
        try {
            $workSchedule = WorkSchedule::with('employee', 'shift')->findOrFail($id);
            
            // Check if this date is a holiday for this employee
            $isOffday = EmployeHoliday::where('employee_id', $workSchedule->employee_id)
                ->where('day_off', $workSchedule->schedule_date)
                ->exists();

            $data = [
                'title' => 'Edit Jadwal Kerja Karyawan',
                'workSchedule' => $workSchedule,
                'employees' => Employee::orderBy('name')->get(),
                'shifts' => Shift::orderBy('name')->get(),
                'isOffday' => $isOffday,
            ];

            return view('pages.master.workschedule.edit', $data);
        } catch (Exception $e) {
            Log::error('WorkScheduleController@edit: ' . $e->getMessage());
            return redirect()->route('workschedule.index')->with([
                'status' => 'Error!', 
                'message' => 'Jadwal Kerja tidak ditemukan'
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'shift_id' => 'required|exists:shifts,id',
                'schedule_date' => 'required|date',
                'is_offday' => 'sometimes|boolean',
            ]);

            $workSchedule = WorkSchedule::findOrFail($id);
            $oldSchedule = $workSchedule->toArray();
            $dateChanged = $workSchedule->schedule_date != $request->schedule_date || 
                          $workSchedule->employee_id != $request->employee_id;

            // Check for duplicate if date or employee changed
            if ($dateChanged) {
                $existingSchedule = WorkSchedule::where('employee_id', $request->employee_id)
                    ->where('schedule_date', $request->schedule_date)
                    ->where('id', '!=', $id)
                    ->first();
                    
                if ($existingSchedule) {
                    throw ValidationException::withMessages([
                        'schedule_date' => 'Jadwal untuk karyawan ini pada tanggal tersebut sudah ada.'
                    ]);
                }
            }

            // Update the schedule
            $workSchedule->update([
                'employee_id' => $request->employee_id,
                'shift_id' => $request->shift_id,
                'schedule_date' => $request->schedule_date,
            ]);
            
            // Handle off day status
            $isOffday = $request->has('is_offday') && $request->is_offday == 1;
            $existingHoliday = EmployeHoliday::where('employee_id', $workSchedule->employee_id)
                ->where('day_off', $oldSchedule['schedule_date'])
                ->first();
            
            // If it was a holiday before but not now, or date changed
            if ($existingHoliday) {
                if (!$isOffday) {
                    // Remove holiday
                    $existingHoliday->delete();
                } elseif ($dateChanged) {
                    // Update holiday date
                    $existingHoliday->update(['day_off' => $request->schedule_date]);
                }
            } 
            // Wasn't a holiday before but is now
            elseif ($isOffday) {
                // Check holiday count first
                $month = Carbon::parse($request->schedule_date)->month;
                $year = Carbon::parse($request->schedule_date)->year;
                
                $holidaysCount = EmployeHoliday::where('employee_id', $request->employee_id)
                    ->whereMonth('day_off', $month)
                    ->whereYear('day_off', $year)
                    ->count();
                
                if ($holidaysCount >= 4) {
                    throw ValidationException::withMessages([
                        'is_offday' => 'Karyawan sudah mencapai batas hari libur (4 kali) dalam bulan ini.'
                    ]);
                }
                
                // Create new holiday
                EmployeHoliday::create([
                    'employee_id' => $request->employee_id,
                    'day_off' => $request->schedule_date,
                ]);
            }

            $workSchedule->refresh();

            activity()
                ->causedBy(Auth::user())
                ->event('updated')
                ->withProperties([
                    'old' => $oldSchedule,
                    'new' => $workSchedule->toArray()
                ])
                ->log("Jadwal Kerja Berhasil Diperbarui");

            return redirect()->route('workschedule.index')->with([
                'status' => 'Success!',
                'message' => 'Berhasil Mengupdate Jadwal Kerja!'
            ]);
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            Log::error('WorkScheduleController@update: ' . $e->getMessage());
            return redirect()->back()->with([
                'status' => 'Error!',
                'message' => 'Gagal Mengupdate Jadwal Kerja: ' . $e->getMessage()
            ])->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $workSchedule = WorkSchedule::findOrFail($id);
            
            // Check if this is a holiday and delete the holiday record if it exists
            $holiday = EmployeHoliday::where('employee_id', $workSchedule->employee_id)
                ->where('day_off', $workSchedule->schedule_date)
                ->first();
            
            if ($holiday) {
                $holiday->delete();
            }

            activity()
                ->causedBy(Auth::user())
                ->event('deleted')
                ->withProperties($workSchedule->toArray())
                ->log("Jadwal Kerja Berhasil Dihapus");

            $workSchedule->delete();

            return response()->json([
                'success' => true,
                'message' => 'Jadwal Kerja Berhasil Dihapus!',
            ]);
        } catch (Exception $e) {
            Log::error('WorkScheduleController@destroy: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus jadwal kerja: ' . $e->getMessage(),
            ]);
        }
    }
}