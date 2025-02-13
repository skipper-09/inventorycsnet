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
                    @can('create-unit-product')
                        <div class="card-header">
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal8"
                                data-action="create" data-proses="{{ route('tasktemplate.store') }}"
                                data-title="{{ $title }}">Tambah {{ $title }}</button>
                        </div>
                    @endcan
                    <div class="card-body">
                        <table id="scroll-sidebar-datatable"
                            class="table dt-responsive nowrap w-100 table-hover table-striped"
                            data-route="{{ route('tasktemplate.getdata') }}"
                            data-has-action-permission="{{ auth()->user()->canany(['update-task-template', 'delete-task-template'])? 'true': 'false' }}">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Deskripsi</th>
                                    @canany(['update-task-template', 'delete-task-template'])   
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

    @include('pages.master.tasktemplate.form')

@endsection

@push('js')
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>

    <!-- Responsive examples -->
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>

    {{-- route datatable init and js definition --}}
    <script src="{{ asset('assets/js/mods/tasktemplate.js') }}"></script>
@endpush
