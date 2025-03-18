<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name','address'
    ];
    protected $primaryKey = 'id';

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function offices()
    {
        return $this->hasMany(Office::class);
    }
}
