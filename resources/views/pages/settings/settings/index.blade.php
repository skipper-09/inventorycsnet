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
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <form class="custom-validation" action="{{ route('setting.update') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nama Aplikasi</label>
                            <input type="text" name="name" value="{{ $setting->name }}" class="form-control" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Logo</label>
                            <div>
                                @if ($setting->logo)
                                <img id="previewImage" src="{{ asset('storage/' . $setting->logo) }}" alt="Preview Logo"
                                    class="img-thumbnail mb-3" style="max-height: 150px;">
                                @else
                                <img id="previewImage" src="#" alt="Preview Logo" class="img-thumbnail mb-3"
                                    style="display: none; max-height: 150px;">
                                @endif
                            </div>
                            <input type="file" name="logo" class="form-control" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <div>
                                <textarea name="address" class="form-control"
                                    rows="5">{{ $setting->address }}</textarea>
                            </div>
                        </div>
                        <div class="mb-0">
                            <div>
                                <button type="submit" class="btn btn-primary waves-effect waves-light me-1">
                                    Submit
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
            <!-- end card -->
        </div><!-- end col-->
    </div>
</div>

@endsection

@push('js')

<script>
    $(document).ready(function() {
        @if(session('status'))
            Swal.fire({
                position: "center",
                icon: "{{ session('status') }}" === "Success!" ? "success" : "error",
                title: "{{ session('status') }}", 
                text: "{{ session('message') }}", 
                showConfirmButton: false,
                timer: 1500
            });
        @endif

        $('input[type="file"]').on('change', function(event) {
            var file = event.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#previewImage').attr('src', e.target.result).show();
                };
                reader.readAsDataURL(file);
            } else {
                $('#previewImage').hide();
            }
        });
    });
</script>


@endpush