<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkSchedule extends Model
{
    protected $fillable = [
        'employee_id','shift_id','schedule_date','is_offdays'
    ];
    protected $primaryKey = 'id';

    public function Employee(){
        return $this->belongsTo(Employee::class,'employee_id','id');
    }

    public function Shift(){
        return $this->belongsTo(Shift::class,'shift_id','id');
    }
}
