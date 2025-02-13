<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AllowanceType extends Model
{
    protected $primaryKey = 'id';

    protected $fillable = [
        "name",
    ];

    public function allowances()
    {
        return $this->hasMany(Allowance::class);
    }
}
