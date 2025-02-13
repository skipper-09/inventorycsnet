<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    protected $primaryKey = "id";

    protected $fillable = [
        "employee_id",
        "salary_month",
        "basic_salary_amount",
        "bonus",
        "deduction",
        "allowance",
        "total_salary",
        "payment_status",
    ];

    public function employee(){
        return $this->belongsTo(Employee::class);
    }
}
