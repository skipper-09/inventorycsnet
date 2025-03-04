<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitProduct extends Model
{
    protected $fillable = [
        'name'
    ];
    protected $primaryKey = 'id';

    public function product()
    {
        return $this->hasMany(Product::class);
    }
}
