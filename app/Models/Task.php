<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $primaryKey = "id";

    protected $fillable = [
        "name",
        "description",
        'status'
    ];

    public function templateTas()
    {
        return $this->hasMany(Template_task::class,'task_id','id');
    }

    public function detailtask(){
        return $this->hasMany(TaskDetail::class);
    }

}
