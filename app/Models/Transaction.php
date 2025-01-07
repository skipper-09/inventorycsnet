<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'branch_id','customer_id','type'
    ];
    protected $primaryKey = 'id';
}
