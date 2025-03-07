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
                        <!-- Tabs -->
                        <ul class="nav nav-tabs mb-3" id="dataTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="today-tab" data-bs-toggle="tab" data-bs-target="#today"
                                    type="button" role="tab" aria-controls="today" aria-selected="true">Hari
                                    Ini</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="all-tab" data-bs-toggle="tab" data-bs-target="#all"
                                    type="button" role="tab" aria-controls="all" aria-selected="false">Semua</button>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content" id="dataTabsContent">
                            <!-- Today's Data Tab -->
                            <div class="tab-pane fade show active" id="today" role="tabpanel"
                                aria-labelledby="today-tab">
                                <!-- Filters -->
                                {{-- <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label" for="FilterAssignment">
                                            Filter Tanggal Penugasan
                                        </label>
                                        <input type="date" id="FilterAssignment" class="form-control" />
                                    </div>
                                </div> --}}

                                <!-- Data Table for Today -->
                                <table id="today-datatable"
                                    class="table dt-responsive nowrap w-100 table-hover table-striped"
                                    data-route="{{ route('assigmentdata.getDataAssign') }}?filter=today"
                                    data-has-action-permission="{{ auth()->user()->canany(['update-task-report', 'delete-task-report'])? 'true': 'false' }}">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal Penugasan</th>
                                            <th>Nama Karyawan</th>
                                            <th>Tugas</th>
                                            <th>Lokasi</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                </table>

                            </div>

                            <!-- All Data Tab -->
                            <div class="tab-pane fade" id="all" role="tabpanel" aria-labelledby="all-tab">
                                <!-- Filters -->
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label" for="FilterAssignmentAll">
                                            Filter Tanggal Penugasan
                                        </label>
                                        <input type="date" id="FilterAssignmentAll" class="form-control" />
                                    </div>
                                </div>

                                <!-- Data Table for All Data -->
                                <table id="all-datatable" class="table dt-responsive nowrap w-100 table-hover table-striped"
                                    data-route="{{ route('assigmentdata.getDataAssign') }}?filter=all"
                                    data-has-action-permission="{{ auth()->user()->canany(['update-task-report', 'delete-task-report'])? 'true': 'false' }}">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal Penugasan</th>
                                            <th>Nama Karyawan</th>
                                            <th>Tugas</th>
                                            <th>Lokasi</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
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
    <script src="{{ asset('assets/js/mods/assigments.js') }}"></script>
@endpush
