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

<style>
    #before_image_preview,
    #after_image_preview {
        border: 1px solid #ddd;
        padding: 10px;
        margin-top: 10px;
        background-color: #f9f9f9;
    }

    #before_image_output,
    #after_image_output {
        width: 100%;
        height: auto;
        border-radius: 5px;
    }
</style>
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
                        <form action="{{ route('assigmentdata.update',['id'=>$employetask->id]) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="mb-3">
                                    <div class="card-header card-header-bordered">
                                        <div class="card-icon">
                                            <i class="fa fa-clipboard-list fs-17 text-muted"></i>
                                        </div>
                                        <h3 class="card-title">Detail Tugas <span class="text-primary">({{
                                                $employetask->taskDetail->task->name }})</span></h3>
                                    </div>
                                    <div class="card-body ">
                                        <div class="row">
                                            <!-- Task Information -->
                                            <div class="col-md-6">
                                                <h5 class="text-primary">{{ $employetask->taskDetail->name }}</h5>
                                                <p>{!! $employetask->taskDetail->description !!}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label w-100" for="report_content">Kegiatan</label>
                                    <textarea class="form-control autosize" name="report_content" rows="3"></textarea>
                                    @error('report_content')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label w-100" for="before_image">Gambar Sebelum</label>
                                            <input type="file" name="before_image"
                                                class="form-control @error('before_image') is-invalid @enderror"
                                                id="before_image" />
                                            @error('before_image')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                            <div id="before_image_preview" class="mt-3" style="display: none;">
                                                <h6>Preview Gambar Sebelum</h6>
                                                <img src="" id="before_image_output" class="img-fluid"
                                                    style="max-width: 100%;" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label w-100" for="after_image">Gambar Sesudah</label>
                                            <input type="file" name="after_image"
                                                class="form-control @error('after_image') is-invalid @enderror"
                                                id="after_image" />
                                            @error('after_image')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                            <div id="after_image_preview" class="mt-3" style="display: none;">
                                                <h6>Preview Gambar Sesudah</h6>
                                                <img src="" id="after_image_output" class="img-fluid"
                                                    style="max-width: 100%;" />
                                            </div>
                                        </div>
                                    </div>
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

<!-- Bootstrap datepicker -->
<script src="{{ asset('assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/form-datepicker.init.js') }}"></script>

<!-- bs custom file input plugin -->
<script src="{{ asset('assets/libs/autosize/autosize.min.js') }}"></script>
<script>
    "use strict"; $(function () { autosize($(".autosize")) });
</script>
<script>
    $('#before_image').change(function(event) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
            $('#before_image_output').attr('src', e.target.result);
            $('#before_image_preview').show();
        }
        
        reader.readAsDataURL(this.files[0]);
    });

    $('#after_image').change(function(event) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
            $('#after_image_output').attr('src', e.target.result);
            $('#after_image_preview').show();
        }
        reader.readAsDataURL(this.files[0]);
    });
</script>

@endpush