<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'unit_id','name','description'
    ];
    protected $primaryKey = 'id';
}
