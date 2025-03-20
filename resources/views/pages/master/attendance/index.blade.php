@extends('layouts.base')
@section('title', $title)

@push('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />

    <!-- Responsive datatable examples -->
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
                    @can('create-attendance')
                        <div class="card-header">
                            <a href="{{ route('attendance.add') }}" class="btn btn-primary btn-sm">Tambah
                                {{ $title }}</a>
                        </div>
                    @endcan
                    <div class="card-body">
                        <div class="row align-items-end g-3 mb-4">
                            <!-- Filter Tanggal -->
                            <div class="col-12 col-md-4">
                                <label class="form-label" for="filter_date">Filter Tanggal</label>
                                <input type="date" id="filter_date" class="form-control filter"
                                    placeholder="Pilih Tanggal">
                            </div>
                            <!-- Filter Karyawan -->
                            <div class="col-12 col-md-4">
                                <label class="form-label" for="filter_employee">Filter Karyawan</label>
                                <select id="filter_employee" class="form-control select2">
                                    <option value="">Semua Karyawan</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Filter Status -->
                            <div class="col-12 col-md-4">
                                <label class="form-label" for="filter_status">Filter Status</label>
                                <select id="filter_status" class="form-control select2">
                                    <option value="">Semua Status</option>
                                    <option value="late_in">Terlambat (Clock In)</option>
                                    <option value="early_out">Pulang Awal</option>
                                    <option value="on_time">Tepat Waktu</option>
                                    <option value="no_checkout">Belum Checkout</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                        data-bs-target="#modal8">
                                        <i class="fas fa-file-excel me-1"></i> Export Excel
                                    </button>
                                </div>
                            </div>
                        </div>
                        <table id="scroll-sidebar-datatable"
                            class="table dt-responsive nowrap w-100 table-hover table-striped"
                            data-route="{{ route('attendance.getdata') }}"
                            data-has-action-permission="{{ auth()->user()->canany(['read-attendance', 'update-attendance', 'delete-attendance'])? 'true': 'false' }}">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Karyawan</th>
                                    <th>Tanggal</th>
                                    <th>Jadwal</th>
                                    <th>Clock In</th>
                                    <th>Clock Out</th>
                                    <th>Status</th>
                                    @canany(['read-attendance', 'update-attendance', 'delete-attendance'])
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

    <!-- Modal Export Excel -->
    <div class="modal fade" id="modal8" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportModalLabel">Export Attendance Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('attendance.export') }}" method="GET">
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date">
                        </div>
                        <div class="mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date">
                        </div>
                        <div class="mb-3">
                            <label for="employee_id" class="form-label">Employee</label>
                            <select class="form-select" id="employee_id" name="employee_id">
                                <option value="">All Employees</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-file-excel me-1"></i> Export
                            </button>
                        </div>
                    </form>
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
    <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-select2.init.js') }}"></script>

    <script src="{{ asset('assets/js/mods/attendance.js') }}"></script>

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
