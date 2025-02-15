<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $primaryKey = "id";

    protected $fillable = [
        "task_template_id",
        "start_date",
        "end_date",
        "status",
    ];

    public function taskTemplate()
    {
        return $this->belongsTo(TaskTemplate::class);
    }
    public function assignes()
    {
        return $this->hasMany(TaskAssign::class);
    }
}
