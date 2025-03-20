<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'unit_id',
        'name',
        'description',
        'is_modem'
    ];
    
    protected $primaryKey = 'id';

    public function unit()
    {
        return $this->belongsTo(UnitProduct::class);
    }

    public function productRoles()
    {
        return $this->hasOne(ProductRole::class);
    }

    public function transactionProduct()
    {
        return $this->hasOne(TransactionProduct::class);
    }
}
