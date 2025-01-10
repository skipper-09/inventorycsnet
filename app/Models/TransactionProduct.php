<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionProduct extends Model
{
    protected $fillable = [
        'transaction_id','product_id','quantity'
    ];
    protected $primaryKey = 'id';


    public function transaksi(){
        return  $this->belongsTo(Transaction::class,'transaction_id','id');
    }

    public function product(){
        return  $this->belongsTo(Product::class,'product_id','id');
    }
}
