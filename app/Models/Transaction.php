<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'branch_id','customer_id','type'
    ];
    protected $primaryKey = 'id';

    public function branch(){
       return $this->belongsTo(Branch::class,'branch_id','id');
    }

    public function customer(){
        return  $this->belongsTo(Customer::class,'customer_id','id');
    }

    public function Transactionproduct(){
        return $this->hasMany(TransactionProduct::class);
    }
}
