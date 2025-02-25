<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskAssign extends Model
{
    protected $primaryKey = "id";

    protected $fillable = [
        "task_template_id",
        "assignee_id",
        "assignee_type",
        'assign_date',
        'place'
    ];

    public function tasktemplate()
    {
        return $this->belongsTo(TaskTemplate::class);
    }

    public function assignee()
    {
        return $this->morphTo();
    }
}
