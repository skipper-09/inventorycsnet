@extends('layouts.base')
@section('title', $title)

@push('css')
<!-- DataTables -->
<link href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet"
    type="text/css" />
<!-- Responsive datatable examples -->
<link href="{{ asset('assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet"
    type="text/css" />
{{-- select 2 --}}
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
                @can('create-product')
                <div class="card-header justify-content-between">
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal8"
                        data-action="create" data-proses="{{ route('produk.store') }}" data-title="{{ $title }}">Tambah
                        {{ $title }}</button>

                    <div class="btn-group me-2">
                        <button class="btn btn-primary">Action</button>
                        <button class="btn btn-primary dropdown-toggle dropdown-toggle-split"
                            data-bs-toggle="dropdown"></button>
                        <div class="dropdown-menu">
                            <a data-bs-toggle="modal" data-bs-target="#modalimport" data-action="create"
                                data-proses="{{ route('produk.import') }}" data-title="{{ $title }}" type="button"
                                class="dropdown-item">Import</a>
                            <a href="{{ route('produk.export') }}" class="dropdown-item">Export</a>
                        </div>
                    </div>
                </div>
                @endcan
                <div class="card-body">
                    <table id="scroll-sidebar-datatable"
                        class="table dt-responsive nowrap w-100 table-hover table-striped"
                        data-route="{{ route('produk.getdata') }}"
                        data-has-action-permission="{{ auth()->user()->canany(['update-product', 'delete-product'])? 'true': 'false' }}">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Deskripsi</th>
                                <th>Unit</th>
                                @canany(['update-product', 'delete-product'])
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

@include('pages.master.product.form')
@include('pages.master.product.import')

@endsection

@push('js')
<!-- Required datatable js -->
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>

<!-- Responsive examples -->
<script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>

{{-- route datatable init and js definition --}}
<script src="{{ asset('assets/js/mods/product.js') }}"></script>
{{-- select 2 deifinition --}}
<script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/form-select2.init.js') }}"></script>
@endpush