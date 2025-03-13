<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeHoliday extends Model
{
    protected $fillable = [
        'employee_id',
        'day_off'
    ];
    protected $primaryKey = 'id';
    public function Employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
}
