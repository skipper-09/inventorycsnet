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


<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">


{{-- datepicker
<link href="{{ asset('assets/libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet"> --}}
@endpush
@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Tambah {{ $title }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('role') }}">{{ $title }}</a></li>
                        <li class="breadcrumb-item active">Tambah {{ $title }}</li>
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
                                    <label class="form-label w-100" for="type">Piih Tipe</label>
                                    <select name="type" id="type"
                                        class="form-control select2form @error('type') is-invalid @enderror">
                                        <option value="">Pilih Tipe</option>
                                        {{-- <option value="departement">Departement</option> --}}
                                        <option value="employee">Karyawan</option>
                                    </select>
                                    @error('type')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="mb-3 d-none" id="departementOptions">
                                    <label class="form-label w-100" for="departement">Piih Departement</label>
                                    <select name="departement" id="departement"
                                        class="form-control select2form @error('departement') is-invalid @enderror">
                                        <option value="">Pilih Departement</option>
                                      @foreach ($departement as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                      @endforeach
                                    </select>
                                    @error('departement')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="mb-3 d-none" id="employeeOptions">
                                    <label class="form-label w-100" for="employee">Piih Karyawan</label>
                                    <select name="employee" id="employee"
                                        class="form-control select2form @error('employee') is-invalid @enderror">
                                        <option value="">Pilih Karyawan</option>
                                       @foreach ($employee as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }} - {{ $item->position->name }}</option>
                                       @endforeach
                                    </select>
                                    @error('employee')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label w-100" for="task">Piih Template Tugas</label>
                                    <select name="task" id="task"
                                        class="form-control select2form @error('task') is-invalid @enderror">
                                        <option value="">Pilih Template</option>
                                       @foreach ($task as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                       @endforeach
                                    </select>
                                    @error('task')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label w-100" for="assign_date">Tanggal Penugasan</label>
                                   <input type="date" id="dates" name="assign_date" class="form-control @error('assign_date') is-invalid @enderror"  />
                                    @error('assign_date')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label w-100" for="place">Lokasi</label>
                                   <input type="text" name="place" class="form-control @error('place') is-invalid @enderror"  />
                                    @error('place')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
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


<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

{{-- <!-- Bootstrap datepicker -->
<script src="{{ asset('assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/form-datepicker.init.js') }}"></script> --}}

<script>
    flatpickr("#dates", {
        mode: "multiple",   
        dateFormat: "Y-m-d", 
    });
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