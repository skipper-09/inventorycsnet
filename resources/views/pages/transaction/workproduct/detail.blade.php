@extends('layouts.base')
@section('title', $title)

@push('css')
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Detail {{ $title }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('workproduct') }}">{{ $title }}</a></li>
                            <li class="breadcrumb-item active">Detail {{ $title }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->
    </div>

    <div class="container-fluid">
        <div class="page-content-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h5 class="card-title mb-4">Informasi Work Product</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0">
                                            <tbody>
                                                <tr>
                                                    <th scope="row">Tanggal Dibuat</th>
                                                    <td>{{ $work->created_at->format('d M Y H:i') }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Nama Pekerjaan</th>
                                                    <td class="text-uppercase">{{ $work->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Cabang</th>
                                                    <td class="text-uppercase">{{ $transaction->branch->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Teknisi Bertugas</th>
                                                    <td class="text-uppercase"> 
                                                        @foreach($transaction->Transactiontechnition as $index => $teknisi)
                                                        {{ $loop->iteration }}. {{ $teknisi->user->name }}<br>
                                                    @endforeach</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Dibuat</th>
                                                    <td class="text-uppercase">{{ $transaction->userTransaction->name }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <h5 class="card-title mb-4">Detail Produk</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" style="width: 50px;">No</th>
                                                    <th>Nama Produk</th>
                                                    <th>Jumlah</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($transaction->Transactionproduct as $index => $item)
                                                <tr>
                                                    <td class="text-center">{{ $index + 1 }}</td>
                                                    <td>{{ $item->product->name }}</td>
                                                    <td>{{ $item->quantity }} {{ $item->product->unit->name }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            {{-- <tfoot>
                                                <tr>
                                                    <th colspan="2" class="text-end">Total Item:</th>
                                                    <th class="text-center">{{ $transfer->Transactionproduct->sum('quantity') }}</th>
                                                </tr>
                                            </tfoot> --}}
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-start gap-2">
                                        <a href="{{ route('workproduct') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left me-1"></i> Kembali
                                        </a>
                                        {{-- @if($transfer->type == 'out')
                                        <a href="{{ route('transfer.edit', $transfer->id) }}" class="btn btn-primary">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </a>
                                        @endif --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection