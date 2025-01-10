<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZoneOdp extends Model
{
    protected $fillable = [
        'name','zone_id','type'
    ];
    protected $primaryKey = 'id';

    public function odps()
    {
        return $this->hasMany(Odp::class, 'zone_id', 'id');
    }
}
