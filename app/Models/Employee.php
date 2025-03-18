<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Employee extends Model
{
    use Notifiable;
    protected $primaryKey = "id";

    protected $fillable = [
        "department_id",
        "position_id",
        "company_id",
        "name",
        "address",
        "phone",
        "email",
        "date_of_birth",
        "gender",
        "nik",
        "identity_card",
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function salaries()
    {
        return $this->hasMany(Salary::class);
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function deductions()
    {
        return $this->hasMany(Deduction::class);
    }

    public function allowances()
    {
        return $this->hasMany(Allowance::class);
    }

    public function taskAssignments()
    {
        return $this->morphMany(TaskAssign::class, 'assignee');
    }
}
