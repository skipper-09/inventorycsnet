<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskSchedule extends Model
{
    protected $primaryKey = "id";

    protected $fillable = [
        "task_id",
        "schedule_type",
        "next_execution",
    ];

    public function task()
    {
        return $this->belongsTo(related: Task::class);
    }
}
