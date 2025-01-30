<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assign extends Model
{
    use HasFactory;

    /**
     * Kolom yang dapat diisi (mass assignable).
     *
     * @var array
     */
    protected $fillable = [
        'owner_id',
        'owner_signature',
        'technitian_id',
        'technitian_signature',
    ];

    /**
     * Relasi ke model User untuk owner.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Relasi ke model User untuk technitian.
     */
    public function technitian()
    {
        return $this->belongsTo(User::class, 'technitian_id');
    }
}