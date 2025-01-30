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
                            <li class="breadcrumb-item"><a href="{{ route('transfer') }}">Pemindahan Barang</a></li>
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
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h5 class="card-title mb-4">Informasi Transfer</h5>
                                    <div class="table-responsive">
                                        <table class="table table-borderless mb-0">
                                            <tbody>
                                                <tr>
                                                    <th scope="row" class="w-25">Tanggal Transfer</th>
                                                    <td>: {{ $transfer->created_at->format('d M Y H:i') }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Dari Cabang</th>
                                                    <td>: {{ $transfer->branch->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Ke Cabang</th>
                                                    <td>: {{ $transfer->tobranch->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Dibuat</th>
                                                    <td>: {{ $transfer->userTransaction->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Penanggung Jawab</th>
                                                    <td>: {{ $transfer->assign->technitian->name }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
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
                                                @foreach($transfer->Transactionproduct as $index => $item)
                                                <tr>
                                                    <td class="text-center">{{ $index + 1 }}</td>
                                                    <td>{{ $item->product->name }}</td>
                                                    <td>{{ $item->quantity }} {{ $item->product->unit->name }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Signature -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="card-title mb-4">Tanda Tangan</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" style="width: 50px;">No</th>
                                                    <th>Nama</th>
                                                    <th>Tanda Tangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-center">1</td>
                                                    <td>Owner</td>
                                                    <td>
                                                        <img src="{{ asset('storage/' . $transfer->assign->owner_signature) }}" class="img-fluid" style="width: 200px; height: 100px;" alt="Signature">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">2</td>
                                                    <td>Penanggung Jawab</td>
                                                    <td>
                                                        <img src="{{ asset('storage/' . $transfer->assign->technitian_signature) }}" class="img-fluid" style="width: 200px; height: 100px;" alt="Signature">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-start gap-2">
                                        <a href="{{ route('transfer') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left me-1"></i> Kembali
                                        </a>
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