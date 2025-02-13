<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $primaryKey = "id";

    protected $fillable = [
        "department_id",
        "employee_id",
        "task_template_id",
        "task_type",
        "status",
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function taskTemplate()
    {
        return $this->belongsTo(TaskTemplate::class);
    }

    public function taskSchedules()
    {
        return $this->hasMany(TaskSchedule::class);
    }
}
