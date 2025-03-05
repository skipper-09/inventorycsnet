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
                        <form action="{{ route('assigmentdata.update',['id'=>$employetask->id]) }}" method="POST"
                            enctype="multipart/form-data">
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
                                {{-- <div class="row">
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
                                </div> --}}


                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label w-100" for="before_image">Capture Before
                                                Image</label>
                                            <video id="videoElement" autoplay></video>
                                            <br>
                                            <button type="button" id="captureBefore" class="btn btn-primary mt-2">Capture Before Image</button>
                                            <button type="button" id="switchCamera" class="btn btn-secondary mt-2">Switch Camera</button>
                                            <input type="hidden" name="before_image" id="before_image">
                                            <div id="before_image_preview" class="mt-3" style="display: none;">
                                                <h6>Preview Gambar Sebelum</h6>
                                                <img src="" id="before_image_output" class="img-fluid" style="max-width: 100%;" />
                                            </div>
                                        </div>
                                    </div>
                                
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label w-100" for="after_image">Capture After
                                                Image</label>
                                            <video id="after_videoElement" autoplay></video>
                                            <br>
                                            <button type="button" id="captureAfter" class="btn btn-primary mt-2">Capture After Image</button>
                                            <button type="button" id="switchAfterCamera" class="btn btn-secondary mt-2">Switch Camera</button>
                                            <input type="hidden" name="after_image" id="after_image">
                                            <div id="after_image_preview" class="mt-3" style="display: none;">
                                                <h6>Preview Gambar Sesudah</h6>
                                                <img src="" id="after_image_output" class="img-fluid" style="max-width: 100%;" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Pemberitahuan jika kamera tidak tersedia -->
                                <div id="cameraError" style="display: none; color: red;">
                                    <p>Perangkat Anda tidak memiliki kamera atau kamera tidak dapat diakses. Silakan periksa pengaturan privasi atau perangkat Anda.</p>
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
{{-- <script>
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
</script> --}}

<script>
    let currentStreamBefore = null;
    let currentStreamAfter = null;
    let currentDeviceIdBefore = null;
    let currentDeviceIdAfter = null;
    let isFrontCameraBefore = false;
    let isFrontCameraAfter = false;

    // Function to start the camera with specific deviceId
    function startCamera(videoElementId, deviceId, isFront, isBefore) {
        const constraints = {
            video: { deviceId: { exact: deviceId } }
        };

        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia(constraints)
                .then(function(stream) {
                    const video = document.getElementById(videoElementId);
                    video.srcObject = stream;

                    if (isBefore) {
                        currentStreamBefore = stream;
                        currentDeviceIdBefore = deviceId;
                    } else {
                        currentStreamAfter = stream;
                        currentDeviceIdAfter = deviceId;
                    }
                })
                .catch(function(error) {
                    console.log('Error accessing camera: ', error);
                    document.getElementById('cameraError').style.display = 'block'; // Menampilkan pemberitahuan error
                });
        } else {
            document.getElementById('cameraError').style.display = 'block'; // Pemberitahuan jika getUserMedia tidak tersedia
        }
    }

    // Switch between front and back camera for "before" camera
    function switchCameraBefore() {
        isFrontCameraBefore = !isFrontCameraBefore;
        getCameraDevices().then(devices => {
            const selectedDevice = devices.find(device => {
                if (isFrontCameraBefore) return device.kind === 'videoinput' && device.label.includes('front');
                else return device.kind === 'videoinput' && device.label.includes('back');
            });

            if (selectedDevice && selectedDevice.deviceId !== currentDeviceIdBefore) {
                stopCurrentStream(true); // Stop "before" stream
                startCamera('videoElement', selectedDevice.deviceId, isFrontCameraBefore, true);
            }
        });
    }

    // Switch between front and back camera for "after" camera
    function switchCameraAfter() {
        isFrontCameraAfter = !isFrontCameraAfter;
        getCameraDevices().then(devices => {
            const selectedDevice = devices.find(device => {
                if (isFrontCameraAfter) return device.kind === 'videoinput' && device.label.includes('front');
                else return device.kind === 'videoinput' && device.label.includes('back');
            });

            if (selectedDevice && selectedDevice.deviceId !== currentDeviceIdAfter) {
                stopCurrentStream(false); // Stop "after" stream
                startCamera('after_videoElement', selectedDevice.deviceId, isFrontCameraAfter, false);
            }
        });
    }

    // Stop the current camera stream
    function stopCurrentStream(isBefore) {
        if (isBefore && currentStreamBefore) {
            const tracks = currentStreamBefore.getTracks();
            tracks.forEach(track => track.stop());
        } else if (!isBefore && currentStreamAfter) {
            const tracks = currentStreamAfter.getTracks();
            tracks.forEach(track => track.stop());
        }
    }

    // Get available camera devices
    function getCameraDevices() {
        return navigator.mediaDevices.enumerateDevices()
            .then(devices => devices.filter(device => device.kind === 'videoinput'));
    }

    // Try to initialize the camera devices
    getCameraDevices().then(devices => {
        const backCameraBefore = devices.find(device => device.label.includes('back'));
        const backCameraAfter = devices.find(device => device.label.includes('back'));

        if (backCameraBefore) {
            startCamera('videoElement', backCameraBefore.deviceId, isFrontCameraBefore, true);
        }

        if (backCameraAfter) {
            startCamera('after_videoElement', backCameraAfter.deviceId, isFrontCameraAfter, false);
        } else {
            // Show error if no camera is found
            document.getElementById('cameraError').style.display = 'block';
        }
    }).catch(function(error) {
        // Handle case where enumerateDevices fails
        console.log('Error detecting camera devices:', error);
        document.getElementById('cameraError').style.display = 'block';
    });

    // Capture "Before" image
    document.getElementById('captureBefore').addEventListener('click', function () {
        const video = document.getElementById('videoElement');
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        const imageData = canvas.toDataURL('image/png');

        // Show the preview
        document.getElementById('before_image_output').src = imageData;
        document.getElementById('before_image_preview').style.display = 'block';
        document.getElementById('before_image').value = imageData;
    });

    // Capture "After" image
    document.getElementById('captureAfter').addEventListener('click', function () {
        const video = document.getElementById('after_videoElement');
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        const imageData = canvas.toDataURL('image/png');

        // Show the preview
        document.getElementById('after_image_output').src = imageData;
        document.getElementById('after_image_preview').style.display = 'block';
        document.getElementById('after_image').value = imageData;
    });

    // Switch "Before" camera
    document.getElementById('switchCamera').addEventListener('click', function () {
        switchCameraBefore();
    });

    // Switch "After" camera
    document.getElementById('switchAfterCamera').addEventListener('click', function () {
        switchCameraAfter();
    });
</script>

@endpush