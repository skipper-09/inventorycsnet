<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkSchedule extends Model
{
    protected $fillable = [
        'employee_id','shift_id','schedule_date','status'
    ];
    
    protected $primaryKey = 'id';

    public function employee(){
        return $this->belongsTo(Employee::class,'employee_id','id');
    }

    public function shift(){
        return $this->belongsTo(Shift::class,'shift_id','id');
    }
}
