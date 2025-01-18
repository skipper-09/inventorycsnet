<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class ProductRole extends Model
{
    protected $fillable = [
        'role_id',
        'product_id',
    ];
    protected $primaryKey = 'id';

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
