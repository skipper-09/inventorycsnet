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
                        <li class="breadcrumb-item"><a href="{{ route('tasktemplate') }}">Task Template</a></li>
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
                    <div class="card-header card-header-bordered">
                        {{-- <h3 class="card-title">Tab</h3> --}}
                        <div class="">
                            <div class="nav nav-tabs card-nav" id="card3-tab">
                                <a class="nav-item nav-link active" id="card3-home-tab" data-bs-toggle="tab"
                                    href="#card-task">Task</a>
                                <a class="nav-item nav-link" id="card3-profile-tab" data-bs-toggle="tab"
                                    href="#card-penugasan">Penugasan</a>
                                {{-- <a class="nav-item nav-link" id="card3-contact-tab" data-bs-toggle="tab"
                                    href="#card3-contact">Contact</a> --}}
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="card-task">
                                <div class="col-12">
                                    <div class="card">
                                        @can('create-unit-product')
                                        <div class="card-header">
                                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#modal8" data-action="create"
                                                data-taskid="{{ $tasktempalte }}"
                                                data-proses="{{ route('task.store') }}" data-title="{{ $title }}">Tambah
                                                Task</button>
                                        </div>
                                        @endcan
                                        <div class="card-body">
                                            <table id="scroll-sidebar-datatable"
                                                class="table dt-responsive nowrap w-100 table-hover table-striped"
                                                data-route="{{ route('task.getdata',['templateid'=>$tasktempalte]) }}"
                                                data-has-action-permission="{{ auth()->user()->canany(['update-task-template', 'delete-task-template'])? 'true': 'false' }}">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Name</th>
                                                        <th>Deskripsi</th>
                                                        <th>status</th>
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
                            <div class="tab-pane fade" id="card-penugasan">
                                <p class="text-muted mb-0">
                                    Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem
                                    Ipsum has been the industry's standard dummy text ever since the 1500s, when an
                                    unknown printer took a
                                    galley of type and scrambled it to make a type specimen book. It has survived not
                                    only five centuries, but also the leap into electronic typesetting, remaining
                                    essentially unchanged
                                </p>
                            </div>
                            {{-- <div class="tab-pane fade" id="card3-contact">
                                <p class="text-muted mb-0">
                                    Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem
                                    Ipsum has been the industry's standard dummy text ever since the 1500s, when an
                                    unknown printer took a
                                    galley of type and scrambled it to make a type specimen book. It has survived not
                                    only five centuries, but also the leap into electronic typesetting, remaining
                                    essentially unchanged. It
                                    was popularised in the 1960s with the release of Letraset sheets containLorem Ipsum
                                    passages, and more recently with desktop publishing software like Aldus PageMaker
                                    including versions of
                                    Lorem Ipsum.
                                </p>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('pages.master.tasktemplate.detail.task.form')
@endsection

@push('js')
<!-- Required datatable js -->
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>

<!-- Responsive examples -->
<script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>

{{-- route datatable init and js definition --}}
<script src="{{ asset('assets/js/mods/task.js') }}"></script>
{{-- select 2 deifinition --}}
<script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/form-select2.init.js') }}"></script>
@endpush