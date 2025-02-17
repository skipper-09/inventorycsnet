<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskTemplate extends Model
{
    protected $primaryKey = "id";

    protected $fillable = [
        "name",
        "description",
        "frequency",
    ];

    public function taksassign()
    {
        return $this->hasMany(TaskAssign::class);
    }
}
