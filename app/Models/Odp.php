<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Odp extends Model
{
    protected $fillable = [
        'zone_id','odp_id','name','latitude','longitude'
    ];
    protected $primaryKey = 'id';
}
