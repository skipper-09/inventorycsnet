<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendatelocation extends Model
{
    
    protected $fillable = [
        'attendance_id','lat','long','status','attendance_type'
    ];
    protected $primaryKey = 'id';
    
    public function Attendance(){
        return $this->belongsTo(Attendance::class,'attendance_id','id');
    }
}
