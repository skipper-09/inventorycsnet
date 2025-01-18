<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransaferProductController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Pemindahan Barang',
        ];
        return view('pages.transaction.transferproduct.index', $data);
}
}