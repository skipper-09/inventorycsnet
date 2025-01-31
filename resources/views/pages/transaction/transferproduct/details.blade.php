@extends('layouts.base')
@section('title', $title)

@push('css')
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Detail {{ $title }}</h4>
                    <div class="page-title-right">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('transfer') }}">Pemindahan Barang</a></li>
                                <li class="breadcrumb-item active">Detail {{ $title }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Transfer Information -->
                        <div class="row mb-5">
                            <div class="col-md-6">
                                <h5 class="card-title mb-4">Informasi Transfer</h5>
                                <div class="d-flex flex-column gap-3">
                                    <div class="d-flex">
                                        <div class="text-muted w-25">Tanggal Transfer</div>
                                        <div>{{ $transfer->created_at->format('d M Y H:i') }}</div>
                                    </div>
                                    <div class="d-flex">
                                        <div class="text-muted w-25">Dari Cabang</div>
                                        <div>{{ $transfer->branch->name }}</div>
                                    </div>
                                    <div class="d-flex">
                                        <div class="text-muted w-25">Ke Cabang</div>
                                        <div>{{ $transfer->tobranch->name }}</div>
                                    </div>
                                    <div class="d-flex">
                                        <div class="text-muted w-25">Dibuat</div>
                                        <div>{{ $transfer->userTransaction->name }}</div>
                                    </div>
                                    <div class="d-flex">
                                        <div class="text-muted w-25">Penanggung Jawab</div>
                                        <div>{{ $transfer->assign->technitian->name }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Product Details -->
                        <div class="row mb-5">
                            <div class="col-12">
                                <h5 class="card-title mb-4">Detail Produk</h5>
                                <div class="list-group">
                                    @foreach($transfer->Transactionproduct as $index => $item)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                                                {{ $item->product->name }}
                                            </div>
                                            <div class="badge bg-secondary">
                                                {{ $item->quantity }} {{ $item->product->unit->name }}
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Signatures -->
                        <div class="row mb-5">
                            <div class="col-12">
                                <h5 class="card-title mb-4">Tanda Tangan</h5>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <h6 class="card-subtitle mb-3 text-muted">
                                                    {{ $transfer->assign->owner->name }}
                                                </h6>
                                                <img src="{{ asset('storage/' . $transfer->assign->owner_signature) }}" 
                                                     class="img-fluid" 
                                                     style="max-height: 100px; object-fit: contain;" 
                                                     alt="Owner Signature">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <h6 class="card-subtitle mb-3 text-muted">
                                                    {{ $transfer->assign->technitian->name }}
                                                </h6>
                                                <img src="{{ asset('storage/' . $transfer->assign->technitian_signature) }}" 
                                                     class="img-fluid" 
                                                     style="max-height: 100px; object-fit: contain;" 
                                                     alt="Technician Signature">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Back Button -->
                        <div class="row">
                            <div class="col-12">
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
@endsection