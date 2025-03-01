<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class employeTaskLog extends Model
{
    protected $fillable = [
        'employe_task_id','log'
    ];
    protected $primaryKey = 'id';

    public function Employetask(){
        return $this->belongsTo(EmployeeTask::class,'employe_task_id','id');
    }
}
