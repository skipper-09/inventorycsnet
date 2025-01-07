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
        $this->belongsTo(TransactionTechnition::class,'transaction_id');
    }

    public function product(){
        $this->belongsTo(Product::class,'product_id');
    }
}
