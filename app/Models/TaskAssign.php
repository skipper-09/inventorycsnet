<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskAssign extends Model
{
    protected $primaryKey = "id";

    protected $fillable = [
        "task_template_id",
        "assignee_id",
        "assigner_id",
        "assignee_type",
        'assign_date',
        'place'
    ];

    public function tasktemplate()
    {
        return $this->belongsTo(TaskTemplate::class,'task_template_id','id');
    }

    public function employeeTasks()
    {
        return $this->hasMany(EmployeeTask::class);
    }

    public function assignee()
    {
        return $this->morphTo();
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigner_id', 'id');
    }
}
