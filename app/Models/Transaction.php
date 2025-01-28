<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'branch_id',
        'to_branch',
        'customer_id',
        'type',
        'purpose',
        'user_id',
        'work_id',
    ];
    protected $primaryKey = 'id';

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function tobranch()
    {
        return $this->belongsTo(Branch::class, 'to_branch', 'id');
    }

    public function customer()
    {
        return  $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function Transactionproduct()
    {
        return $this->hasMany(TransactionProduct::class);
    }
    public function Transactiontechnition()
    {
        return $this->hasMany(TransactionTechnition::class);
    }
    public function userTransaction()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
    public function WorkTransaction()
    {
        return $this->belongsTo(Work::class,'work_id','id');
    }
}
