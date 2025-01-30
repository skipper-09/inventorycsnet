@extends('layouts.base')
@section('title', $title)

@push('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />

    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
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
                    @can('create-transfer-product')
                        <div class="card-header">
                            <a href="{{ route('transfer.add') }}" class="btn btn-primary btn-sm">Tambah {{ $title }}</a>
                        </div>
                    @endcan
                    <div class="card-body">
                        <div class="row align-items-end g-3 mb-5">
                            <!-- Filter Tanggal -->
                            <div class="col-12 col-md-4">
                                <label class="form-label" for="created_at">Filter Tanggal</label>
                                <input type="date" id="created_at" class="form-control filter"
                                    placeholder="Pilih Tanggal">
                            </div>
                        </div>
                        <table id="scroll-sidebar-datatable"
                            class="table dt-responsive nowrap w-100 table-hover table-striped"
                            data-route="{{ route('transfer.getdata') }}"
                            data-has-action-permission="{{ auth()->user()->canany(['read-transfer-product', 'update-transfer-product', 'delete-transfer-product'])? 'true': 'false' }}">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Dari Cabang</th>
                                    <th>Tujuan Cabang</th>
                                    <th>Barang</th>
                                    <th>Tanggal</th>
                                    @canany(['read-transfer-product', 'update-transfer-product', 'delete-transfer-product'])
                                        <th>Action</th>
                                    @endcanany
                                </tr>
                            </thead>
                        </table>
                    </div> <!-- end card body-->
                </div> <!-- end card -->
            </div><!-- end col-->
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
    <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-select2.init.js') }}"></script>

    <script src="{{ asset('assets/js/mods/transferproduct.js') }}"></script>

    <script>
        @if (Session::has('message'))
            Swal.fire({
                title: `{{ Session::get('status') }}`,
                text: `{{ Session::get('message') }}`,
                icon: "{{ session('status') }}" === "Success!" ? "success" : "error",
                showConfirmButton: false,
                timer: 1500
            });
        @endif
    </script>
@endpush
