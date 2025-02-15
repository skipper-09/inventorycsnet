<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskAssign extends Model
{
    protected $primaryKey = "id";

    protected $fillable = [
        "task_id",
        "assignee_id",
        "assignee_type"
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function assignee()
    {
        return $this->morphTo();
    }
}
