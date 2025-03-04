<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $primaryKey = "id";

    protected $fillable = [
        "department_id",
        "position_id",
        "name",
        "address",
        "phone",
        "email",
        "date_of_birth",
        "gender",
        "nik",
        "identity_card",
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function salaries()
    {
        return $this->hasMany(Salary::class);
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function deductions()
    {
        return $this->hasMany(Deduction::class);
    }

    public function allowances()
    {
        return $this->hasMany(Allowance::class);
    }

    public function tasks()
    {
        return $this->hasManyThrough(
            Task::class, // Target model
            TaskAssign::class, // Intermediate model
            'assignee_id', // Foreign key on the intermediate model
            'id', // Foreign key on the target model
            'id', // Local key on this model
            'task_id' // Local key on the intermediate model
        )->where('task_assigns.assignee_type', 'employee');
    }

    public function taskAssignments()
    {
        return $this->morphMany(TaskAssign::class, 'assignee');
    }
}
