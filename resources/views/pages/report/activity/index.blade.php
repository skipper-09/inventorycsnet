@extends('layouts.base')

@section('title', $title)

@push('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />

    <!-- Select2 -->
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />

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
                    @can('create-activity-report')
                        <div class="card-header">
                            <a href="{{ route('activityreport.add') }}" class="btn btn-primary btn-sm">Tambah
                                {{ $title }}</a>
                        </div>
                    @endcan
                    <div class="card-body">
                        <div class="row align-items-end g-3 mb-5">
                            <div class="col-12 col-md-4">
                                <label class="form-label" for="created_at">Filter Tanggal</label>
                                <input type="date" id="created_at" class="form-control" />
                            </div>
                            @can('export-activity-report')
                                <div class="col-12 col-md-8">
                                    <div class="d-flex justify-content-end">
                                        <button data-bs-toggle="modal" data-bs-target="#modal8" id="export-button"
                                            class="btn btn-outline-success">
                                            <i class="fas fa-file-excel me-2"></i>Export Excel
                                        </button>
                                    </div>
                                </div>
                            @endcan
                        </div>
                        <table id="scroll-sidebar-datatable"
                            class="table dt-responsive nowrap w-100 table-hover table-striped"
                            data-route="{{ route('activityreport.getdata') }}"
                            data-has-action-permission="{{ auth()->user()->canany(['update-activity-report', 'delete-activity-report'])? 'true': 'false' }}">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Jabatan</th>
                                    <th>Laporan</th>
                                    <th>Tanggal Dibuat</th>
                                    @canany(['update-activity-report', 'delete-activity-report'])
                                        <th>Action</th>
                                    @endcanany
                                </tr>
                            </thead>
                        </table>
                    </div> <!-- end card body -->
                </div> <!-- end card -->
            </div><!-- end col -->
        </div>
    </div>

    <div class="modal fade" id="viewActivityReportModal" tabindex="-1" role="dialog"
        aria-labelledby="viewActivityReportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between">
                    <h5 class="modal-title" id="viewActivityReportModalLabel">Laporan</h5>
                    <button type="button" class="btn btn-sm btn-label-danger btn-icon" data-bs-dismiss="modal">
                        <i class="mdi mdi-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="fullActivityReport"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal8" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportModalLabel">Export Laporan Aktivitas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('activityreport.export') }}" method="GET">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="date_from" class="form-label">Tanggal Mulai</label>
                                    <input type="date" name="date_from" id="date_from" class="form-control">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="date_to" class="form-label">Tanggal Selesai</label>
                                    <input type="date" name="date_to" id="date_to" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-file-excel me-1"></i> Export
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>

    <!-- Select2 -->
    <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>

    {{-- route datatable init and js definition --}}
    <script src="{{ asset('assets/js/mods/activityreport.js') }}"></script>

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
