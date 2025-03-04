<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FreeReport extends Model
{
    protected $fillable = [
        'user_id','report_activity'
    ];
    protected $primaryKey = 'id';

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }
}
