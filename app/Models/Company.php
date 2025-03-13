<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name','address'
    ];
    protected $primaryKey = 'id';

    public function companies()
    {
        return $this->hasMany(Company::class);
    }
}
