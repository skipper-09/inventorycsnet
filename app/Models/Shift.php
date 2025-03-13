<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Shift extends Model
{
    protected $fillable = [
        'code','name','shift_start','shift_end','status'
    ];

    protected $primaryKey = 'id';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($shift) {
            if (empty($shift->code)) {
                $shift->code = Str::upper(Str::random(6));
            }
        });
    }
}
