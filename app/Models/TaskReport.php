<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskReport extends Model
{
    protected $primaryKey = "id";

    protected $fillable = [
        "task_assign_id",
        "report_content",
        "reason_not_complated",
    ];

    public function taskassign()
    {
        return $this->belongsTo(TaskAssign::class);
    }

    public function employeeTask()
    {
        return $this->belongsTo(EmployeeTask::class);
    }

    public function reportImage(){
        return $this->hasMany(ReportImage::class);
    }
}
