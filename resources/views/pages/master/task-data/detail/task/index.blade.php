@extends('layouts.base')
@section('title', $title)

@push('css')
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    
    {{-- Select2 --}}
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">{{ $title }} - <span class="text-primary">{{ $name }}</span></h4>
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
                        <div class="card-body">
                            <div class="tab-pane fade show active" id="card-task">
                                <div class="col-12">
                                    <div class="card">
                                        @can('create-detail-task')
                                            <div class="card-header d-flex gap-2">
                                                <a href="{{ route('taskdata') }}" class="btn btn-info btn-sm">Kembali</a>
                                                <a href="{{ route('taskdetail.add', ['taskdataid' => $taskdata]) }}" class="btn btn-primary btn-sm">Tambah {{ $title }}</a>
                                            </div>
                                        @endcan
                                        <div class="card-body">
                                            <table id="scroll-sidebar-datatable"
                                                class="table dt-responsive nowrap w-100 table-hover table-striped"
                                                data-route="{{ route('taskdetail.getdata', ['taskdataid' => $taskdata]) }}"
                                                data-has-action-permission="{{ auth()->user()->canany(['update-detail-task', 'delete-detail-task']) ? 'true' : 'false' }}">
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

    <!-- Modal for full description -->
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
    {{-- Select2 definition --}}
    <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-select2.init.js') }}"></script>
    <script src="{{ asset('assets/libs/tinymce/tinymce.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Handle showing full description in modal
            $(document).on('click', '.show-full-description', function() {
                var description = $(this).data('description');
                $('#fullDescription').html(description);
            });
        });
    </script>
@endpush