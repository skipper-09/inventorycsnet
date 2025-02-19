<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deduction extends Model
{
    protected $primaryKey = "id";

    protected $fillable = [
        "employee_id",
        "deduction_type_id",
        "amount",
        "created_at",
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function deductionType()
    {
        return $this->belongsTo(DeductionType::class);
    }
}
