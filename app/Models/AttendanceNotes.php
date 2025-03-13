<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceNotes extends Model
{
    protected $fillable = [
        'attendance_id','notes','attendance_type'
    ];
    protected $primaryKey = 'id';

    public function Attendance(){
        return $this->belongsTo(Attendance::class,'attendance_id','id');
    }
}
