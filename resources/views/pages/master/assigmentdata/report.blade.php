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


{{-- datepicker --}}
<link href="{{ asset('assets/libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
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
                        <li class="breadcrumb-item"><a href="{{ route('role') }}">{{ $title }}</a></li>
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
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('assignment.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="mb-3">
                                            <div class="card-header card-header-bordered">
                                                <div class="card-icon">
                                                    <i class="fa fa-clipboard-list fs-17 text-muted"></i>
                                                </div>
                                                <h3 class="card-title">Detail Tugas <span class="text-primary">({{ $employetask->taskDetail->task->name }})</span></h3>
                                            </div>
                                            <div class="card-body ">
                                                <div class="row">
                                                    <!-- Task Information -->
                                                    <div class="col-md-6">
                                                        <h5 class="text-primary">{{ $employetask->taskDetail->name }}</h5>
                                                        <p>{{ $employetask->taskDetail->description }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                </div>
                                <div class="row">
<div class="col-md-6">
    <div class="mb-3">
        <label class="form-label w-100" for="assign_date">Tanggal Penugasan</label>
        <input type="date" name="assign_date"
            class="form-control @error('assign_date') is-invalid @enderror" />
        @error('assign_date')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror
    </div>
</div>
<div class="col-md-6">
    <div class="mb-3">
        <label class="form-label w-100" for="assign_date">Tanggal Penugasan</label>
        <input type="date" name="assign_date"
            class="form-control @error('assign_date') is-invalid @enderror" />
        @error('assign_date')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror
    </div>
</div>
                                </div>
                                
                                {{-- <div class="mb-3">
                                    <label class="form-label w-100" for="place">Lokasi</label>
                                    <input type="text" name="place"
                                        class="form-control @error('place') is-invalid @enderror" />
                                    @error('place')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div> --}}
                            </div>
                            <div>
                                <button class="btn btn-primary" type="submit">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
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
{{-- select 2 deifinition --}}
<script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/form-select2.init.js') }}"></script>

<!-- Bootstrap datepicker -->
<script src="{{ asset('assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/form-datepicker.init.js') }}"></script>

<script>
    $(document).ready(function() {
        // Set the initial state based on the preselected value, if any
        toggleOptions($('#type').val());

        // When the user changes the dropdown selection
        $('#type').change(function() {
            var selectedType = $(this).val();
            toggleOptions(selectedType);
        });

        // Function to show or hide the appropriate sections based on the selection
        function toggleOptions(selectedType) {
            // If the selected type is "departement", show the department options
            if (selectedType == 'departement') {
                $('#departementOptions').removeClass("d-none");
                $('#employeeOptions').addClass("d-none");
            }
            // If the selected type is "employee", show the employee options
            else if (selectedType == 'employee') {
                $('#employeeOptions').removeClass("d-none");
                $('#departementOptions').addClass("d-none");
            } 
            // If no valid selection is made, hide both options
            else {
                $('#departementOptions').addClass("d-none");
                $('#employeeOptions').addClass("d-none");
            }
        }
    });
</script>


@endpush