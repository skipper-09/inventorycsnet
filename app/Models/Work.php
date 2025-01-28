<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    protected $fillable = [
        'name'
    ];
    protected $primaryKey = 'id';

    public function Transaction()
    {
        return $this->hasMany(Transaction::class);
    }
}
