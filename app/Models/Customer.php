<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'branch_id','zone_id','odp_id','name','phone','address','latitude','longitude','sn_modem'
    ];
    protected $primaryKey = 'id';

    public function zone(){
        return  $this->belongsTo(ZoneOdp::class);
    }

    // public function odp(){
    //     return $this->belongsTo(Odp::class);
    // }

    public function branch(){
        return $this->belongsTo(Branch::class);
    }

    public function transaction(){
        return $this->hasOne(Transaction::class);
    }
}
