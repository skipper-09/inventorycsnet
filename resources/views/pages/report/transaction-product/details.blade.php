@extends('layouts.base')
@section('title', $title)

@section('content')
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">{{ $title }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('report.transaction-product') }}">Laporan Transaksi</a></li>
                            <li class="breadcrumb-item active">{{ $title }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaction Details -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Informasi Transaksi</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th>Tujuan Transaksi</th>
                                <td>
                                    @if($transaction->purpose == 'psb')
                                        Pemasangan Baru
                                    @elseif($transaction->purpose == 'repair')
                                        Perbaikan
                                    @elseif($transaction->purpose == 'transfer')
                                        Transfer
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Dari Cabang</th>
                                <td>{{ $transaction->branch->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Ke Cabang</th>
                                <td>{{ $transaction->tobranch->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Dibuat Pada</th>
                                <td>{{ $transaction->created_at->format('H:i d-m-Y') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Details -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Detail Produk</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 50px;">No</th>
                                        <th>Barang</th>
                                        <th>Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transaction->transactionproduct as $index => $product)
                                        <tr>
                                            <td class="text-center" style="width: 50px">{{ $index + 1 }}</td>
                                            <td>{{ $product->product->name ?? 'N/A' }}</td>
                                            <td>{{ $product->quantity }} {{ $product->product->unit->name ?? '' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('report.transaction-product') }}" class="btn btn-secondary">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
