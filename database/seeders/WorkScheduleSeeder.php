<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Shift;
use App\Models\WorkSchedule;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class WorkScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get all employees and shifts
        $employees = Employee::all();
        $shifts = Shift::all();
        
        if ($employees->isEmpty()) {
            $this->command->info('No employees found. Please run employee seeder first.');
            return;
        }
        
        if ($shifts->isEmpty()) {
            $this->command->info('No shifts found. Please run shift seeder first.');
            return;
        }

        // Create work schedules for the current week for each employee
        $today = Carbon::today();
        $startOfWeek = $today->copy()->startOfWeek(); // Monday
        
        foreach ($employees as $employee) {
            // Regular workday shifts (Monday to Friday)
            for ($i = 0; $i < 5; $i++) {
                $date = $startOfWeek->copy()->addDays($i);
                
                // Assign regular morning shift (shift_id = 1)
                WorkSchedule::create([
                    'employee_id' => $employee->id,
                    'shift_id' => 1, // Using the first shift (Regular Morning)
                    'schedule_date' => $date->format('Y-m-d'),
                    'is_offdays' => false,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
            
            // Weekend days are off days for most employees
            // Saturday
            WorkSchedule::create([
                'employee_id' => $employee->id,
                'shift_id' => null, // No shift for off days
                'schedule_date' => $startOfWeek->copy()->addDays(5)->format('Y-m-d'),
                'is_offdays' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            
            // Sunday
            WorkSchedule::create([
                'employee_id' => $employee->id,
                'shift_id' => null, // No shift for off days
                'schedule_date' => $startOfWeek->copy()->addDays(6)->format('Y-m-d'),
                'is_offdays' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            
            // Some employees may have weekend shifts instead of off days
            if ($employee->id % 2 == 0) { // Every second employee works on weekends
                // Update Saturday to working day
                WorkSchedule::where('employee_id', $employee->id)
                    ->where('schedule_date', $startOfWeek->copy()->addDays(5)->format('Y-m-d'))
                    ->update([
                        'shift_id' => 4, // Weekend shift
                        'is_offdays' => false
                    ]);
                
                // Update Sunday to working day
                WorkSchedule::where('employee_id', $employee->id)
                    ->where('schedule_date', $startOfWeek->copy()->addDays(6)->format('Y-m-d'))
                    ->update([
                        'shift_id' => 4, // Weekend shift
                        'is_offdays' => false
                    ]);
            }
            
            // Also create some schedules for next week
            $nextWeekStart = $startOfWeek->copy()->addWeek();
            
            // Weekdays for next week
            for ($i = 0; $i < 5; $i++) {
                $date = $nextWeekStart->copy()->addDays($i);
                
                // Randomly assign shifts for weekdays
                $shiftId = rand(1, 3); // Shifts 1-3 for weekdays
                
                WorkSchedule::create([
                    'employee_id' => $employee->id,
                    'shift_id' => $shiftId,
                    'schedule_date' => $date->format('Y-m-d'),
                    'is_offdays' => false,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
            
            // Weekend days for next week are off days by default
            // Next Saturday
            WorkSchedule::create([
                'employee_id' => $employee->id,
                'shift_id' => null,
                'schedule_date' => $nextWeekStart->copy()->addDays(5)->format('Y-m-d'),
                'is_offdays' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            
            // Next Sunday
            WorkSchedule::create([
                'employee_id' => $employee->id,
                'shift_id' => null,
                'schedule_date' => $nextWeekStart->copy()->addDays(6)->format('Y-m-d'),
                'is_offdays' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            
            // Some random off days during weekdays (e.g., leave days)
            if ($employee->id % 5 == 0) { // Every fifth employee has a day off next week
                $randomDay = rand(0, 4); // Random weekday (Monday to Friday)
                
                WorkSchedule::where('employee_id', $employee->id)
                    ->where('schedule_date', $nextWeekStart->copy()->addDays($randomDay)->format('Y-m-d'))
                    ->update([
                        'shift_id' => null,
                        'is_offdays' => true
                    ]);
            }
        }
    }
}