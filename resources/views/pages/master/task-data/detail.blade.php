@extends('layouts.base')
@section('title', $title)

@push('css')
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
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
                            <li class="breadcrumb-item"><a href="{{ route('taskdata') }}">Data Task</a></li>
                            <li class="breadcrumb-item active">{{ $title }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->
    </div>

    <div class="container-fluid">
        <div class="page-content-wrapper">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        {{-- <div class="card-header card-header-bordered">
                        <div class="">
                            <div class="nav nav-tabs card-nav" id="card3-tab">
                                <a class="nav-item nav-link active" id="card3-home-tab" data-bs-toggle="tab"
                                    href="#card-task">Task</a>
                            </div>
                        </div>
                    </div> --}}
                        <div class="card-body">

                            <div class="tab-pane fade show active" id="card-task">
                                <div class="col-12">
                                    <div class="card">
                                        @can('create-unit-product')
                                            <div class="card-header">
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('taskdata') }}"><button
                                                            class="btn btn-sm btn-info">Kembali</button></a>
                                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                        data-bs-target="#modal8" data-action="create"
                                                        data-taskid="{{ $taskdata }}"
                                                        data-proses="{{ route('taskdetail.store') }}"
                                                        data-title="{{ $title }}">Tambah
                                                        Task</button>
                                                </div>
                                            </div>
                                        @endcan
                                        <div class="card-body">
                                            <table id="scroll-sidebar-datatable"
                                                class="table dt-responsive nowrap w-100 table-hover table-striped"
                                                data-route="{{ route('taskdetail.getdata', ['taskdataid' => $taskdata]) }}"
                                                data-has-action-permission="{{ auth()->user()->canany(['update-detail-task', 'delete-detail-task'])? 'true': 'false' }}">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Name</th>
                                                        <th>Deskripsi</th>
                                                        @canany(['update-detail-task', 'delete-detail-task'])
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewDescriptionModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between">
                    <h5 class="modal-title">Detail Deskripsi</h5>
                    <button type="button" class="btn btn-sm btn-label-danger btn-icon" data-bs-dismiss="modal">
                        <i class="mdi mdi-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="fullDescription"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    @include('pages.master.task-data.detail.task.form')
@endsection

@push('js')
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>

    <!-- Responsive examples -->
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>

    {{-- route datatable init and js definition --}}
    <script src="{{ asset('assets/js/mods/taskdetail.js') }}"></script>
    {{-- select 2 deifinition --}}
    <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-select2.init.js') }}"></script>
    <script src="{{ asset('assets/libs/tinymce/tinymce.min.js') }}"></script>
@endpush