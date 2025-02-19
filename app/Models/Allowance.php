<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Allowance extends Model
{
    protected $primaryKey = "id";

    protected $fillable = [
        "employee_id",
        "allowance_type_id",
        "amount",
        "created_at",
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function allowanceType()
    {
        return $this->belongsTo(AllowanceType::class);
    }
}
