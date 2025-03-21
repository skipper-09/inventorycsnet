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
                                <th>Tanggal</th>
                                <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Tujuan Transaksi</th>
                                <td>
                                    @if($transaction->purpose == 'psb')
                                        Pemasangan Baru
                                    @elseif($transaction->purpose == 'repair')
                                        Perbaikan
                                    @elseif($transaction->purpose == 'transfer')
                                        Transfer
                                    @elseif($transaction->purpose == 'other')
                                        <span class="text-uppercase">{{ $transaction->WorkTransaction->name }}</span>
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
                                <th>Dibuat</th>
                                <td>{{ $transaction->userTransaction->name}}</td>
                            </tr>
                            <tr>
                                <th scope="row">Teknisi Bertugas</th>
                                <td class="text-uppercase"> 
                                    @foreach($transaction->Transactiontechnition as $index => $teknisi)
                                    {{ $loop->iteration }}. {{ $teknisi->user->name }}<br>
                                @endforeach</td>
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
