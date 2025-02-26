<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskReport extends Model
{
    protected $primaryKey = "id";
    
    protected $fillable = [
        "employee_task_id",
        "report_content",
        "reason_not_complated",
    ];
    
    public function taskAssign()
    {
        return $this->belongsTo(TaskAssign::class);
    }
    
    public function employeeTask()
    {
        return $this->belongsTo(EmployeeTask::class, 'employee_task_id');
    }
    
    public function reportImage(){
        return $this->hasMany(ReportImage::class, 'report_task_id');
    }
}