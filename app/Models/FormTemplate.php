<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class FormTemplate extends Model
{
    protected $fillable = [
        'role_id',
        'name',
        'content'
    ];
    protected $primaryKey = 'id';

    protected $casts = [
        'content' => 'array'
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
