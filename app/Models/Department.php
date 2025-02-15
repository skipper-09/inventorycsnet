<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $primaryKey = 'id';
    
    protected $fillable = [
        "name",
        "location",
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function taskAssignments()
    {
        return $this->morphMany(TaskAssign::class,'assignee');
    }
}
