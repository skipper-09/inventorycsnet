<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    protected $fillable = [
        'company_id','name','lat','long','radius','address'
    ];
    protected $primaryKey = 'id';

    public function company(){
        return $this->belongsTo(Company::class,'company_id','id');
    }
}
