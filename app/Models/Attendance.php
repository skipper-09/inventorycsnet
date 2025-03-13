<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'employee_id','work_schedule_id','clock_in','clock_out','clock_in_status','clock_out_status','clock_in_image','clock_out_image'
    ];
    protected $primaryKey = 'id';

    public function Employee(){
        return $this->belongsTo(Employee::class,'employee_id','id');
    }
    public function WorkSchedule(){
        return $this->belongsTo(WorkSchedule::class,'work_schedule_id','id');
    }

    public function AttendanceNotes(){
        return $this->hasMany(AttendanceNotes::class);
    }
}
