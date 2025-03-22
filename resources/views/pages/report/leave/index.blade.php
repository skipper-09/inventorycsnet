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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
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
                    @can('create-leave-report')
                        <div class="card-header">
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal8"
                                data-action="create" data-proses="{{ route('leavereport.store') }}"
                                data-title="{{ $title }}">
                                {{ Auth::user()->hasRole('Employee') ? 'Ajukan Cuti' : 'Tambah ' . $title }}
                            </button>
                        </div>
                    @endcan
                    <div class="card-body">
                        <div class="row mb-3">
                            <!-- Filter by Date -->
                            <div class="col-md-4">
                                <label class="form-label" for="created_at">
                                    Filter Tanggal
                                </label>
                                <input type="date" id="created_at" class="form-control" />
                            </div>
                            <!-- Filter by Year -->
                            <div class="col-md-4">
                                <label class="form-label" for="year">
                                    Filter Tahun
                                </label>
                                <select id="year" class="form-select select2">
                                    <option value="">-- Pilih Tahun --</option>
                                    @for ($i = date('Y'); $i >= 2021; $i--)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <table id="scroll-sidebar-datatable"
                            class="table dt-responsive nowrap w-100 table-hover table-striped"
                            data-route="{{ route('leavereport.getdata') }}"
                            data-has-action-permission="{{ auth()->user()->canany(['update-leave-report', 'delete-leave-report'])? 'true': 'false' }}">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Karyawan</th>
                                    <th>Jabatan</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Sampai</th>
                                    @canany(['update-leave-report', 'delete-leave-report'])
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

    @include('pages.report.leave.form')

@endsection

@push('js')
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>

    <!-- Responsive examples -->
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    {{-- route datatable init and js definition --}}
    <script src="{{ asset('assets/js/mods/leavereport.js') }}"></script>
    {{-- select 2 deifinition --}}
    <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-select2.init.js') }}"></script>
@endpush


{{-- @extends('layouts.base')
@section('title', $title)

@push('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />

    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
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
                        <table id="scroll-sidebar-datatable"
                            class="table dt-responsive nowrap w-100 table-hover table-striped"
                            data-route="{{ route('leavereport.getdata') }}"
                            data-has-action-permission="{{ auth()->user()->canany(['update-unit-product', 'delete-unit-product'])? 'true': 'false' }}">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Karyawan</th>
                                    <th>Jabatan</th>
                                    <th>Tanggal</th>
                                    <th>Sampai</th>
                                    <th>Alasan</th>
                                    <th>Tahun</th>
                                    <th>Status</th>
                                    @canany(['update-unit-product', 'delete-unit-product'])   
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

    @include('pages.report.leave.form')

@endsection

@push('js')
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>

    <!-- Responsive examples -->
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>

    <script src="{{ asset('assets/js/mods/leavereport.js') }}"></script>
      <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
      <script src="{{ asset('assets/js/pages/form-select2.init.js') }}"></script>
@endpush --}}
