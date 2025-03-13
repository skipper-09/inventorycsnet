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
                    @can('create-office')
                    <div class="card-header">
                        <a href="{{ route('office.add') }}" class="btn btn-primary btn-sm">Tambah
                            {{ $title }}</a>
                    </div>
                    @endcan
                    <div class="card-body">
                        <table id="scroll-sidebar-datatable"
                            class="table dt-responsive nowrap w-100 table-hover table-striped"
                            data-route="{{ route('office.getdata') }}"
                            data-has-action-permission="{{ auth()->user()->canany(['update-office', 'delete-office'])? 'true': 'false' }}">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Kantor</th>
                                    <th>Perusahaan</th>
                                    <th>Alamat</th>
                                    <th>Lokasi</th>
                                    <th>Radius</th>
                                    @canany(['update-office', 'delete-office'])
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
    <script src="{{ asset('assets/js/mods/office.js') }}"></script>
@endpush
