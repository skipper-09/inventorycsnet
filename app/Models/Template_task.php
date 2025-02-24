<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template_task extends Model
{
    protected $fillable = [
        'task_template_id','task_id'
    ];
    protected $primaryKey = 'id';

    public function tasktemplate()
    {
        return $this->belongsTo(TaskTemplate::class,'task_template_id','id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
