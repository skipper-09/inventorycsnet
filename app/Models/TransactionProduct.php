<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionProduct extends Model
{
    protected $fillable = [
        'transaction_id',
        'product_id',
        'quantity'
    ];

    protected $primaryKey = 'id';

    public function transaksi()
    {
        return  $this->belongsTo(Transaction::class, 'transaction_id', 'id');
    }

    public function product()
    {
        return  $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function getTransactionPurpose()
    {
        switch ($this->transaksi->purpose) {
            case 'psb':
                return '<div class="badge badge-label-primary">Pemasangan Baru</div>';
            case 'repair':
                return '<div class="badge badge-label-warning">Perbaikan</div>';
            case 'transfer':
                return '<div class="badge badge-label-info">Transfer</div>';
            case 'other':
                return '<div class="badge badge-label-info">Other</div>';
            default:
                return '<span class="badge badge-label-success">Stok Masuk</span>';
        }
    }

    public function getTransactionPurposeText()
    {
        switch ($this->transaksi->purpose) {
            case 'psb':
                return 'Pemasangan Baru';
            case 'repair':
                return 'Perbaikan';
            case 'transfer':
                return 'Transfer';
            case 'other':
                return 'Other';
            default:
                return 'Stok Masuk';
        }
    }
}
