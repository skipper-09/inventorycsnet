<!-- Camera Component -->
@php
    // The parameters allow us to customize each camera instance
    $mode = $mode ?? 'before'; // 'before' or 'after'
    $id = $id ?? 'camera_' . $mode;
    $label = $label ?? ($mode == 'before' ? 'Capture Before Image' : 'Capture After Image');
    $previewLabel = $previewLabel ?? ($mode == 'before' ? 'Preview Gambar Sebelum' : 'Preview Gambar Sesudah');
    $fieldName = $fieldName ?? $mode . '_image';
@endphp

<div class="mb-3 camera-wrapper" data-id="{{ $id }}">
    <label class="form-label w-100">{{ $label }}</label>

    <!-- Mobile Camera (File Input) -->
    <div class="camera-mobile">
        <div class="mb-2">
            <input type="file" id="fileInput_{{ $id }}" accept="image/*" capture="environment" class="d-none"
                onchange="handleCameraCapture('{{ $id }}')">

            <button type="button" id="cameraButton_{{ $id }}" class="btn btn-primary"
                onclick="document.getElementById('fileInput_{{ $id }}').click()">
                <i class="fa fa-camera me-1"></i> {{ $label }}
            </button>

            {{-- <button type="button" id="switchCamera_{{ $id }}" 
                    class="btn btn-secondary ms-2"
                    onclick="toggleCameraMode('{{ $id }}')">
                <i class="fa fa-sync me-1"></i> Switch Camera
            </button> --}}
        </div>
    </div>

    <!-- Desktop Camera (Canvas) -->
    {{-- <div class="camera-desktop" style="display: none;">
        <video id="video_{{ $id }}" class="w-100" style="max-height: 300px; background: #000;" autoplay playsinline></video>
        <canvas id="canvas_{{ $id }}" style="display: none;"></canvas>
        
        <div class="mt-2">
            <button type="button" class="btn btn-primary capture-btn" data-id="{{ $id }}">
                <i class="fa fa-camera me-1"></i> {{ $label }}
            </button>
            
            <button type="button" class="btn btn-secondary ms-2 switch-cam-btn" data-id="{{ $id }}">
                <i class="fa fa-sync me-1"></i> Switch Camera
            </button>
        </div>
    </div> --}}
    <div class="camera-desktop" style="display: none">
        <div class="mb-2">
            <input type="file" id="fileInput_{{ $id }}" accept="image/*" capture="environment"
                class="d-none" onchange="handleCameraCapture('{{ $id }}')">

            <button type="button" id="cameraButton_{{ $id }}" class="btn btn-primary"
                onclick="document.getElementById('fileInput_{{ $id }}').click()">
                <i class="fa fa-camera me-1"></i> {{ $label }}
            </button>

            {{-- <button type="button" id="switchCamera_{{ $id }}" 
                    class="btn btn-secondary ms-2"
                    onclick="toggleCameraMode('{{ $id }}')">
                <i class="fa fa-sync me-1"></i> Switch Camera
            </button> --}}
        </div>
    </div>

    <input type="hidden" name="{{ $fieldName }}" id="input_{{ $id }}" required>

    <div id="preview_{{ $id }}" class="mt-3 text-center" style="display: none;">
        <h6>{{ $previewLabel }}</h6>
        <img src="" id="img_{{ $id }}" class="img-fluid" style="max-height: 300px;" />
    </div>

    @error($fieldName)
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
