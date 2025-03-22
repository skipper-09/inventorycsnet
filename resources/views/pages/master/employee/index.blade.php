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
                    @can('create-employee')
                        <div class="card-header">
                            <a href="{{ route('employee.add') }}" class="btn btn-primary btn-sm">Tambah
                                {{ $title }}</a>
                        </div>
                    @endcan
                    <div class="card-body">
                        <div class="row mb-5 d-flex align-items-center">
                            {{-- <div class="col-md-4">
                                <!-- Filter Position (Select2) -->
                                <label for="filter-position" class="form-label">Filter Jabatan</label>
                                <select class="form-select select2" id="position_id" style="width: 100%">
                                    <option value="">Semua Jabatan</option>
                                    @foreach ($positions as $position)
                                        <option value="{{ $position->id }}">{{ $position->name }}</option>
                                    @endforeach
                                </select>
                            </div> --}}
                            <div class="col-md-4">
                                <!-- Filter Department (Select2) -->
                                <label for="filter-department" class="form-label">Filter Departemen</label>
                                <select class="form-select select2" id="department_id" style="width: 100%">
                                    <option value="">Semua Departemen</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>                        
                        <table id="scroll-sidebar-datatable"
                            class="table dt-responsive nowrap w-100 table-hover table-striped"
                            data-route="{{ route('employee.getdata') }}"
                            data-has-action-permission="{{ auth()->user()->canany(['read-employee', 'update-employee', 'delete-employee'])? 'true': 'false' }}">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Jabatan</th>
                                    <th>Departemen</th>
                                    @canany(['read-employee', 'update-employee', 'delete-employee'])
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

    {{-- select 2 deifinition --}}
    <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-select2.init.js') }}"></script>

    {{-- route datatable init and js definition --}}
    <script src="{{ asset('assets/js/mods/employee.js') }}"></script>

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
