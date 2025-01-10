<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'name',
        'address'
    ];
    protected $primaryKey = 'id';

    public function transaction()
    {
        return $this->hasMany(Branch::class);
    }
}
