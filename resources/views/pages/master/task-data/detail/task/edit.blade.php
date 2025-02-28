@extends('layouts.base')

@section('title', $title)

@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Edit {{ $title }}</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('taskdetail.index', ['taskdataid' => $taskdata->task_id]) }}">{{ $title }}</a></li>
                            <li class="breadcrumb-item active">Edit {{ $title }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-label-danger alert-dismissible fade show mb-4" role="alert">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="ri-error-warning-fill fs-16 align-middle me-2"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <strong class="d-block mb-1">Please check the following errors:</strong>
                                        <div class="text-muted">
                                            @foreach ($errors->all() as $error)
                                                <div class="mb-1">{{ $error }}</div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            </div>
                        @endif

                        <!-- Form for editing -->
                        <form action="{{ route('taskdetail.update', ['id' => $taskdata->id]) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                            @csrf
                            @method('PUT') <!-- For PUT request to update data -->
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label required">Nama</label>
                                        <input class="form-control @error('name') is-invalid @enderror" type="text" name="name" id="name" value="{{ old('name', $taskdata->name) }}" placeholder="Nama Task" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label required">Deskripsi</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" name="description" id="description" rows="10" placeholder="Deskripsi Task" required>{{ old('description', $taskdata->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <button type="submit" class="btn btn-primary">Update Task</button>
                                        <a href="{{ route('taskdetail.index', ['taskdataid' => $taskdata->task_id]) }}" class="btn btn-secondary">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </form>
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

    <script src="{{ asset('assets/js/mods/taskdetail.js') }}"></script>
    <script src="{{ asset('assets/libs/tinymce/tinymce.min.js') }}"></script>
    <script>
        // Initialize TinyMCE for the description field
        tinymce.init({
            selector: '#description',
            height: 300,
            menubar: false,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table paste code help wordcount'
            ],
            toolbar: 'undo redo | formatselect | ' +
            'bold italic backcolor | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist outdent indent | ' +
            'removeformat | help',
        });
    </script>
@endpush