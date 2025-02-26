{{-- @extends('layouts.base')

@section('title', $title)

@push('css')
    <style>
        .card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .modal-content {
            border-radius: 10px;
        }

        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .modal-title {
            font-weight: 600;
        }

        .btn-info {
            background-color: #17a2b8;
            border-color: #17a2b8;
        }

        .btn-info:hover {
            background-color: #138496;
            border-color: #117a8b;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Page title -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">{{ $title }}</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">{{ $title }}</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Reports list -->
        <div class="row">
            <div class="col-12">
                @if (count($taskReports) > 0)
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                        @foreach ($taskReports as $report)
                            <div class="col">
                                <div class="card shadow-sm h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">Report #{{ $report['id'] }}</h5>
                                        <h6 class="card-subtitle mb-2 text-muted">
                                            Task: {{ $report['task']->name ?? 'Untitled Task' }}
                                        </h6>
                                        <p class="card-text">
                                            <strong>Employee:</strong>
                                            {{ $report['employee']['name'] ?? 'Not Assigned' }}
                                        </p>
                                        <p class="card-text">
                                            <strong>Report Type:</strong> {{ ucfirst($report['report_type']) }}
                                        </p>
                                        <p class="card-text">
                                            <strong>Assigned Date:</strong> {{ formatDate($report['taskAssign']['assign_date']) }}
                                        </p>
                                        <p class="card-text">
                                            <strong>Status:</strong> {!! $report['statusBadge'] !!}
                                        </p>
                                        <a href="#" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#detailModal{{ $report['id'] }}">View Details</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Detail Modal -->
                            <div class="modal fade" id="detailModal{{ $report['id'] }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Report #{{ $report['id'] }} -
                                                {{ $report['task']->name ?? 'Untitled Task' }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6 class="border-bottom pb-2 mb-3">Report Details</h6>
                                                    <div class="mb-3">
                                                        <strong>Type:</strong>
                                                        {{ ucfirst($report['report_type']) }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <strong>Content:</strong>
                                                        <p class="mt-2">{{ $report['report_content'] }}</p>
                                                    </div>
                                                    @if ($report['report_image'])
                                                        <div>
                                                            <strong>Image:</strong>
                                                            <div class="mt-2">
                                                                <a href="{{ asset('storage/' . $report['report_image']) }}"
                                                                    target="_blank" class="image-popup">
                                                                    <img src="{{ asset('storage/' . $report['report_image']) }}"
                                                                        alt="Report Image" class="img-thumbnail"
                                                                        style="max-width: 150px;">
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="col-md-6">
                                                    <h6 class="border-bottom pb-2 mb-3">Assignment Details</h6>
                                                    <div class="mb-2">
                                                        <strong>ID:</strong> {{ $report['taskAssign']['id'] }}
                                                    </div>
                                                    <div class="mb-2">
                                                        <strong>Date:</strong> {{ $report['taskAssign']['assign_date'] }}
                                                    </div>
                                                    <div class="mb-2">
                                                        <strong>Employee:</strong>
                                                        {{ $report['employee']['name'] ?? 'Not Assigned' }}
                                                    </div>
                                                    <div class="mb-2">
                                                        <strong>Task:</strong>
                                                        {{ $report['task']->name ?? 'Untitled Task' }}
                                                    </div>
                                                    <div>
                                                        <strong>Description:</strong>
                                                        <p class="mt-2">
                                                            {{ $report['task']->description ?? 'No description available' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <a href="#" class="btn btn-primary">Edit Report</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="card shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="fa fa-file-alt fs-3 text-muted mb-3"></i>
                            <h5>No Task Reports Available</h5>
                            <p class="text-muted">There are no task reports to display at this time.</p>
                            <a href="#" class="btn btn-primary btn-sm mt-2">Create New Report</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assets/js/mods/taskreport.js') }}"></script>
@endpush --}}

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
                    <div class="card-body">
                        <table id="scroll-sidebar-datatable"
                            class="table dt-responsive nowrap w-100 table-hover table-striped"
                            data-route="{{ route('taskreport.getdata') }}"
                            data-has-action-permission="{{ auth()->user()->canany(['update-task-report', 'delete-task-report'])? 'true': 'false' }}">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Penugasan</th>
                                    <th>Nama Karyawan</th>
                                    <th>Lokasi</th>
                                    @canany(['update-task-report', 'delete-task-report'])
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

    {{-- route datatable init and js definition --}}
    <script src="{{ asset('assets/js/mods/taskreport.js') }}"></script>
@endpush
