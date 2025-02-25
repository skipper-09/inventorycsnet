<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeTask extends Model
{
    protected $fillable = [
        'task_detail_id','employee_id','task_assign_id','status'
    ];
    protected $primaryKey = 'id';

    public function taskDetail()
    {
        return $this->belongsTo(TaskDetail::class,'task_detail_id','id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class,'employee_id','id');
    }
}
