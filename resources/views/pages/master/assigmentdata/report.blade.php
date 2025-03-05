@extends('layouts.base')
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
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <form id="taskReportForm"
                                    action="{{ route('assigmentdata.update', ['id' => $employetask->id]) }}" method="POST">
                                    @csrf
                                    @method('PUT')

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
                                                <div class="col-md-6">
                                                    <h5 class="text-primary">{{ $employetask->taskDetail->name }}</h5>
                                                    <p>{!! $employetask->taskDetail->description !!}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Camera Capture Sections -->
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label class="form-label w-100">Capture Before Image</label>
                                                <div class="camera-container text-center">
                                                    <video id="videoElement" class="img-fluid mx-auto"
                                                        style="max-height: 300px;"></video>
                                                    <div class="camera-controls mt-2">
                                                        <button type="button" id="captureBefore"
                                                            class="btn btn-primary">Capture Before Image</button>
                                                        <button type="button" id="switchCamera"
                                                            class="btn btn-secondary ms-2">Switch Camera</button>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="before_image" id="before_image" required>
                                                <div id="before_image_preview" class="mt-3 text-center"
                                                    style="display: none;">
                                                    <h6>Preview Gambar Sebelum</h6>
                                                    <img src="" id="before_image_output" class="img-fluid"
                                                        style="max-height: 300px;" />
                                                </div>
                                                @error('before_image')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label class="form-label w-100">Capture After Image</label>
                                                <div class="camera-container text-center">
                                                    <video id="after_videoElement" class="img-fluid mx-auto"
                                                        style="max-height: 300px;"></video>
                                                    <div class="camera-controls mt-2">
                                                        <button type="button" id="captureAfter"
                                                            class="btn btn-primary">Capture After Image</button>
                                                        <button type="button" id="switchCamera_after"
                                                            class="btn btn-secondary ms-2">Switch Camera</button>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="after_image" id="after_image" required>
                                                <div id="after_image_preview" class="mt-3 text-center"
                                                    style="display: none;">
                                                    <h6>Preview Gambar Sesudah</h6>
                                                    <img src="" id="after_image_output" class="img-fluid"
                                                        style="max-height: 300px;" />
                                                </div>
                                                @error('after_image')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Camera Error Display -->
                                        <div id="cameraError" class="col-12" style="display: none; color: red;">
                                            <p>Perangkat Anda tidak memiliki kamera atau kamera tidak dapat diakses. Silakan
                                                periksa pengaturan privasi atau perangkat Anda.</p>
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
    <!-- Existing JS includes -->
    <script>
        "use strict";

        class SharedCameraManager {
            constructor() {
                this.devices = [];
                this.activeStream = null;
                this.currentDeviceIndex = 0;
                this.cameraError = document.getElementById('cameraError');
            }

            async initializeDevices() {
                try {
                    const devices = await navigator.mediaDevices.enumerateDevices();
                    this.devices = devices.filter(device => device.kind === 'videoinput');

                    // Prioritaskan kamera belakang
                    this.devices.sort((a, b) => {
                        const aLabel = a.label.toLowerCase();
                        const bLabel = b.label.toLowerCase();

                        if (aLabel.includes('back') || aLabel.includes('rear')) return -1;
                        if (bLabel.includes('back') || bLabel.includes('rear')) return 1;
                        if (aLabel.includes('front') || aLabel.includes('user')) return 1;
                        if (bLabel.includes('front') || bLabel.includes('user')) return -1;

                        return 0;
                    });

                    console.log('Kamera tersedia:', this.devices.map(device => device.label));
                } catch (error) {
                    this.showError(`Gagal mendapatkan perangkat: ${error.message}`);
                }
            }

            async startCamera(videoElement, switchButton) {
                // Hentikan stream yang sedang aktif
                if (this.activeStream) {
                    this.activeStream.getTracks().forEach(track => track.stop());
                }

                try {
                    const constraints = {
                        video: {
                            deviceId: this.devices[this.currentDeviceIndex].deviceId ? {
                                exact: this.devices[this.currentDeviceIndex].deviceId
                            } : true
                        }
                    };

                    const stream = await navigator.mediaDevices.getUserMedia(constraints);
                    videoElement.srcObject = stream;
                    this.activeStream = stream;

                    // Update label tombol switch
                    this.updateSwitchButtonText(switchButton);

                    // Pastikan video diputar
                    await videoElement.play();

                    return stream;
                } catch (error) {
                    this.showError(`Kesalahan mengakses kamera: ${error.message}`);
                    return null;
                }
            }

            switchCamera(videoElement, switchButton) {
                if (this.devices.length <= 1) {
                    alert('Hanya satu kamera yang tersedia');
                    return;
                }

                // Pindah ke perangkat kamera berikutnya
                this.currentDeviceIndex = (this.currentDeviceIndex + 1) % this.devices.length;

                // Mulai kamera baru untuk video yang dipilih
                this.startCamera(videoElement, switchButton);
            }

            updateSwitchButtonText(switchButton) {
                const currentDevice = this.devices[this.currentDeviceIndex];
                const deviceLabel = currentDevice.label.toLowerCase();

                if (deviceLabel.includes('front') || deviceLabel.includes('user')) {
                    switchButton.textContent = 'Ganti ke Kamera Belakang';
                } else if (deviceLabel.includes('back') || deviceLabel.includes('rear')) {
                    switchButton.textContent = 'Ganti ke Kamera Depan';
                } else {
                    switchButton.textContent = `Ganti Kamera (${this.currentDeviceIndex + 1})`;
                }
            }

            showError(message) {
                console.error(message);
                if (this.cameraError) {
                    this.cameraError.textContent = message;
                    this.cameraError.style.display = 'block';
                }
            }
        }

        // Inisialisasi manajemen kamera
        document.addEventListener('DOMContentLoaded', async () => {
            const sharedCameraManager = new SharedCameraManager();
            await sharedCameraManager.initializeDevices();

            // Konfigurasi kamera sebelum
            const beforeVideoElement = document.getElementById('videoElement');
            const beforeSwitchButton = document.getElementById('switchCamera');
            const beforeCaptureButton = document.getElementById('captureBefore');
            const beforeImagePreview = document.getElementById('before_image_output');
            const beforeHiddenInput = document.getElementById('before_image');

            // Konfigurasi kamera sesudah
            const afterVideoElement = document.getElementById('after_videoElement');
            const afterSwitchButton = document.getElementById('switchCamera_after');
            const afterCaptureButton = document.getElementById('captureAfter');
            const afterImagePreview = document.getElementById('after_image_output');
            const afterHiddenInput = document.getElementById('after_image');

            // Fungsi capture gambar
            const captureImage = (videoElement, imagePreview, hiddenInput) => {
                const canvas = document.createElement('canvas');
                canvas.width = videoElement.videoWidth || 640;
                canvas.height = videoElement.videoHeight || 480;

                const context = canvas.getContext('2d');
                context.drawImage(videoElement, 0, 0, canvas.width, canvas.height);

                const imageData = canvas.toDataURL('image/png');
                imagePreview.src = imageData;
                imagePreview.closest('div').style.display = 'block';
                hiddenInput.value = imageData;
            };

            // Mulai kamera sebelum
            await sharedCameraManager.startCamera(beforeVideoElement, beforeSwitchButton);

            // Event listener untuk switch kamera sebelum
            beforeSwitchButton.addEventListener('click', () => {
                sharedCameraManager.switchCamera(beforeVideoElement, beforeSwitchButton);
            });

            // Event listener untuk capture gambar sebelum
            beforeCaptureButton.addEventListener('click', () => {
                captureImage(beforeVideoElement, beforeImagePreview, beforeHiddenInput);
            });

            // Mulai kamera sesudah
            await sharedCameraManager.startCamera(afterVideoElement, afterSwitchButton);

            // Event listener untuk switch kamera sesudah
            afterSwitchButton.addEventListener('click', () => {
                sharedCameraManager.switchCamera(afterVideoElement, afterSwitchButton);
            });

            // Event listener untuk capture gambar sesudah
            afterCaptureButton.addEventListener('click', () => {
                captureImage(afterVideoElement, afterImagePreview, afterHiddenInput);
            });

            // Validasi form
            const form = document.getElementById('taskReportForm');
            form.addEventListener('submit', function(event) {
                const beforeImage = document.getElementById('before_image').value;
                const afterImage = document.getElementById('after_image').value;
                const reportContent = document.querySelector('textarea[name="report_content"]').value;

                if (!beforeImage) {
                    event.preventDefault();
                    alert('Harap ambil gambar sebelum terlebih dahulu');
                    return;
                }

                if (!afterImage) {
                    event.preventDefault();
                    alert('Harap ambil gambar sesudah terlebih dahulu');
                    return;
                }

                if (!reportContent.trim()) {
                    event.preventDefault();
                    alert('Harap isi deskripsi kegiatan');
                    return;
                }
            });
        });
    </script>
@endpush
