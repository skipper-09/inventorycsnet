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
                    <div class="card-body">
                        <!-- Filters -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="FilterAssignment">Tanggal Penugasan</label>
                                <input type="date" id="FilterAssignment" class="form-control" />
                            </div>
                            <div class="col-md-4">
                                <label for="FilterStatus">Status</label>
                                <select id="FilterStatus" class="form-control select2 filter">
                                    <option value="">Pilih Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="complated">Completed</option>
                                    <!-- Add other status options here -->
                                </select>
                            </div>
                        </div>

                        <!-- Data Table -->
                        <table id="scroll-sidebar-datatable"
                            class="table dt-responsive nowrap w-100 table-hover table-striped"
                            data-route="{{ route('taskreport.getdata') }}"
                            data-has-action-permission="{{ auth()->user()->canany(['update-task-report', 'delete-task-report'])? 'true': 'false' }}">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Penugasan</th>
                                    <th>Nama Karyawan</th>
                                    <th>Tugas</th>
                                    <th>Lokasi</th>
                                    @canany(['update-task-report', 'delete-task-report'])
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
    <script src="{{ asset('assets/js/mods/taskreport.js') }}"></script>
@endpush
