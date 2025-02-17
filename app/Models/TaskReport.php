<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskReport extends Model
{
    protected $primaryKey = "id";

    protected $fillable = [
        "task_assign_id",
        "report_type",
        "report_image",
        "report_content",
    ];

    public function taskassign()
    {
        return $this->belongsTo(TaskAssign::class);
    }
}
