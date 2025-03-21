<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $primaryKey = 'id';

    protected $fillable = [
        "name",
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
