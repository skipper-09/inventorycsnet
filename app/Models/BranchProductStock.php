<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchProductStock extends Model
{
    protected $fillable = [
        'branch_id','product_id','stock'
    ];
    protected $primaryKey = 'id';
}
