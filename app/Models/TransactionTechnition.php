<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionTechnition extends Model
{
    protected $fillable = [
        'transaction_id','user_id'
    ];
    protected $primaryKey = 'id';


    public function transaksi(){
        $this->belongsTo(TransactionTechnition::class,'transaction_id');
    }

    public function user(){
        $this->belongsTo(User::class,'user_id');
    }
}
