<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskDetail extends Model
{
    protected $fillable = [
        'task_id','name','description'
    ];
    protected $primaryKey = 'id';

    public function task()
    {
        return $this->belongsTo(Task::class,'task_id','id');
    }
}
