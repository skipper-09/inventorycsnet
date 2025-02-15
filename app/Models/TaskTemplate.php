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

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
