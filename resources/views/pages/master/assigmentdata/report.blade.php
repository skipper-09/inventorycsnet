{{-- @extends('layouts.base')
@section('title', $title)

@push('css')
    <!-- Existing CSS links -->
    <style>
        .camera-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        .camera-container video,
        .camera-container img {
            max-width: 100%;
            max-height: 300px;
            object-fit: contain;
        }

        .camera-controls {
            margin-top: 10px;
            display: flex;
            justify-content: center;
        }

        #before_image_preview,
        #after_image_preview {
            margin-top: 15px;
            text-align: center;
        }

        .webcam-active video {
            background-color: #000;
        }

        .fallback-active .camera-controls {
            margin-top: 2rem;
            margin-bottom: 2rem;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Page Title -->
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

        <div class="container-fluid">
            <div class="page-content-wrapper">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <!-- Error message display -->
                                <div id="cameraError" style="display: none; color: red;">
                                    <p>Your device does not have a camera or camera access is denied. Please check your
                                        privacy settings or device.</p>
                                </div>

                                <!-- Task Details -->
                                <div class="mb-3">
                                    <div class="card-header card-header-bordered">
                                        <div class="card-icon">
                                            <i class="fa fa-clipboard-list fs-17 text-muted"></i>
                                        </div>
                                        <h3 class="card-title">Detail Tugas <span
                                                class="text-primary">({{ $employetask->taskDetail->task->name }})</span>
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h5 class="text-primary">{{ $employetask->taskDetail->name }}</h5>
                                                <p>{!! $employetask->taskDetail->description !!}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <form id="taskReportForm"
                                    action="{{ route('assigmentdata.update', ['id' => $employetask->id]) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="row">
                                        <!-- Before Camera Section -->
                                        <div class="col-md-6">
                                            @include('layouts.partials.camera', [
                                                'mode' => 'before',
                                                'id' => 'before_cam',
                                                'label' => 'Capture Before Image',
                                                'previewLabel' => 'Preview Gambar Sebelum',
                                                'fieldName' => 'before_image',
                                            ])
                                        </div>

                                        <!-- After Camera Section -->
                                        <div class="col-md-6">
                                            @include('layouts.partials.camera', [
                                                'mode' => 'after',
                                                'id' => 'after_cam',
                                                'label' => 'Capture After Image',
                                                'previewLabel' => 'Preview Gambar Sesudah',
                                                'fieldName' => 'after_image',
                                            ])
                                        </div>
                                    </div>

                                    <!-- Report Content -->
                                    <div class="mb-3">
                                        <label class="form-label w-100" for="report_content">Kegiatan</label>
                                        <textarea class="form-control autosize" name="report_content" rows="3" required></textarea>
                                        @error('report_content')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Submit Button -->
                                    <div>
                                        <button type="submit" class="btn btn-primary" id="submitButton">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <!-- Add camera manager script -->
    <script src="{{ asset('assets/js/camera-manager.js') }}"></script>

    <script>
        "use strict";

        // Form validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('taskReportForm');
            form.addEventListener('submit', function(event) {
                const beforeImage = document.getElementById('input_before_cam').value;
                const afterImage = document.getElementById('input_after_cam').value;
                const reportContent = document.querySelector('textarea[name="report_content"]').value;

                if (!beforeImage) {
                    event.preventDefault();
                    alert('Please capture the before image first');
                    return;
                }

                if (!afterImage) {
                    event.preventDefault();
                    alert('Please capture the after image second');
                    return;
                }

                if (!reportContent.trim()) {
                    event.preventDefault();
                    alert('Please fill in the activity description');
                    return;
                }
            });
        });
    </script>
@endpush --}}

@extends('layouts.base')
@section('title', $title)

@push('css')
    <!-- Existing CSS links -->
    <style>
        .image-preview-container {
            margin-top: 15px;
            text-align: center;
        }

        .image-preview-container img {
            max-width: 100%;
            max-height: 300px;
            object-fit: contain;
        }

        .image-upload-section {
            margin-bottom: 20px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Page Title -->
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

        <div class="container-fluid">
            <div class="page-content-wrapper">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <!-- Task Details -->
                                <div class="mb-3">
                                    <div class="card-header card-header-bordered">
                                        <div class="card-icon">
                                            <i class="fa fa-clipboard-list fs-17 text-muted"></i>
                                        </div>
                                        <h3 class="card-title">Detail Tugas <span
                                                class="text-primary">({{ $employetask->taskDetail->task->name }})</span>
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h5 class="text-primary">{{ $employetask->taskDetail->name }}</h5>
                                                <p>{!! $employetask->taskDetail->description !!}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <form id="taskReportForm"
                                    action="{{ route('assigmentdata.update', ['id' => $employetask->id]) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <div class="row">
                                        <!-- Before Image File Upload Section -->
                                        <div class="col-md-6">
                                            <div class="image-upload-section">
                                                <label class="form-label w-100" for="before_image">Before Image</label>
                                                <input type="file" class="form-control" id="before_image" name="before_image" 
                                                    accept="image/*" required onchange="previewImage(this, 'before_image_preview')">
                                                <div id="before_image_preview" class="image-preview-container"></div>
                                            </div>
                                        </div>

                                        <!-- After Image File Upload Section -->
                                        <div class="col-md-6">
                                            <div class="image-upload-section">
                                                <label class="form-label w-100" for="after_image">After Image</label>
                                                <input type="file" class="form-control" id="after_image" name="after_image" 
                                                    accept="image/*" required onchange="previewImage(this, 'after_image_preview')">
                                                <div id="after_image_preview" class="image-preview-container"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Report Content -->
                                    <div class="mb-3">
                                        <label class="form-label w-100" for="report_content">Kegiatan</label>
                                        <textarea class="form-control autosize" name="report_content" rows="3" required></textarea>
                                        @error('report_content')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Submit Button -->
                                    <div>
                                        <button type="submit" class="btn btn-primary" id="submitButton">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        "use strict";

        // Function to preview uploaded images
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            preview.innerHTML = '';
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.classList.add('mt-2');
                    preview.appendChild(img);
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Form validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('taskReportForm');
            form.addEventListener('submit', function(event) {
                const beforeImage = document.getElementById('before_image').value;
                const afterImage = document.getElementById('after_image').value;
                const reportContent = document.querySelector('textarea[name="report_content"]').value;

                if (!beforeImage) {
                    event.preventDefault();
                    alert('Please upload the before image first');
                    return;
                }

                if (!afterImage) {
                    event.preventDefault();
                    alert('Please upload the after image');
                    return;
                }

                if (!reportContent.trim()) {
                    event.preventDefault();
                    alert('Please fill in the activity description');
                    return;
                }
            });
        });
    </script>
@endpush