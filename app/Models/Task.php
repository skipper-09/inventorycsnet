<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $primaryKey = "id";

    protected $fillable = [
        "task_template_id",
        "name",
        "description",
        'status'
    ];

    public function taskTemplate()
    {
        return $this->belongsTo(TaskTemplate::class);
    }
}
