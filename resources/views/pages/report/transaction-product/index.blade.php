@extends('layouts.base')
@section('title', $title)

@push('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <!-- Select2 -->
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">{{ $title }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">{{ $title }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-end g-3 mb-5">
                            <!-- Filter Jenis Transaksi -->
                            <div class="col-12 col-md-4">
                                <label class="form-label" for="FilterTransaction">
                                    Filter Jenis Transaksi <span class="text-danger">*</span>
                                </label>
                                <select class="form-select select2 filter" id="FilterTransaction" name="branch_id">
                                    <option value="">Pilih Jenis Transaksi</option>
                                    @foreach ($purposes as $purpose)
                                        @if ($purpose !== 'stock_in')
                                            <option value="{{ $purpose }}" @if (request('purpose') == $purpose) selected @endif>
                                                {{ $purpose == 'psb'
                                                    ? 'Pemasangan Baru'
                                                    : ($purpose == 'repair'
                                                        ? 'Perbaikan'
                                                        : ucwords(str_replace('_', ' ', $purpose))) }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                
                            <!-- Filter Tanggal -->
                            <div class="col-12 col-md-4">
                                <label class="form-label" for="created_at">Filter Tanggal</label>
                                <input type="date" id="created_at" class="form-control filter" placeholder="Pilih Tanggal">
                            </div>
                
                            <!-- Export Button -->
                            <div class="col-12 col-md-4">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('report.transaction-product.export') }}" class="btn btn-outline-success">
                                        <i class="fas fa-file-excel me-2"></i>Export Excel
                                    </a>
                                </div>
                            </div>
                        </div>
                        <table id="scroll-sidebar-datatable"
                            class="table dt-responsive nowrap w-100 table-hover table-striped"
                            data-route="{{ route('report.transaction-product.getdata') }}">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Jenis Transaksi</th>
                                    <th>Barang</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <!-- Responsive examples -->
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('assets/libs/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-select2.init.js') }}"></script>

    <!-- Custom JS for Transaction Product -->
    <script src="{{ asset('assets/js/mods/transactionproduct.js') }}"></script>
@endpush
