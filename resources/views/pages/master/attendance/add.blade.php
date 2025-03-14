@extends('layouts.base')

@section('title', $title)

@push('css')
    <!-- Select2 -->
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">{{ $title }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('attendance') }}">Absensi Karyawan</a></li>
                            <li class="breadcrumb-item active">Tambah</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- End page title -->

        <div class="page-content-wrapper">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Form Tambah Absensi</h4>
                        </div>
                        <div class="card-body">
                            @if (session('status'))
                                <div class="alert alert-{{ session('status') == 'Success!' ? 'success' : 'danger' }} alert-dismissible fade show"
                                    role="alert">
                                    {{ session('message') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="ri-error-warning-fill fs-16 align-middle me-2"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <strong class="d-block mb-1">Silakan periksa kesalahan berikut:</strong>
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

                            <form action="{{ route('attendance.store') }}" method="POST" class="form form-vertical"
                                id="attendanceForm" enctype="multipart/form-data">
                                @csrf
                                <div class="form-body">
                                    <div class="row">
                                        <!-- Employee Selection -->
                                        <div class="col-12 mb-3">
                                            <label class="form-label" for="employee_id">Karyawan <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select @error('employee_id') is-invalid @enderror"
                                                id="employee_id" name="employee_id" required>
                                                <option value="">Pilih Karyawan</option>
                                                @foreach ($employees as $employee)
                                                    <option value="{{ $employee->id }}"
                                                        {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                                        {{ $employee->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('employee_id')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <!-- Attendance Date -->
                                        <div class="col-12 mb-3">
                                            <label class="form-label" for="attendance_date">Tanggal Absensi <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" id="attendance_date"
                                                class="form-control @error('attendance_date') is-invalid @enderror"
                                                name="attendance_date"
                                                value="{{ old('attendance_date', $today ?? date('Y-m-d')) }}" required>
                                            @error('attendance_date')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <!-- Work Schedule -->
                                        <div class="col-12 mb-3">
                                            <label class="form-label" for="work_schedule_id">Jadwal Kerja</label>
                                            <select class="form-select @error('work_schedule_id') is-invalid @enderror"
                                                id="work_schedule_id" name="work_schedule_id">
                                                <option value="">Pilih Jadwal</option>
                                                <!-- Options will be loaded dynamically -->
                                            </select>
                                            @error('work_schedule_id')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                            <div id="schedule-loading" class="text-muted mt-1" style="display: none;">
                                                <i class="fas fa-spinner fa-spin"></i> Memuat jadwal...
                                            </div>
                                            <div id="no-schedule-message" class="text-muted mt-1" style="display: none;">
                                                <i class="fas fa-info-circle"></i> Tidak ada jadwal untuk karyawan ini
                                                pada tanggal tersebut. Absensi tetap dapat dibuat.
                                            </div>
                                        </div>

                                        <!-- Clock In Time -->
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="clock_in">Waktu Clock In <span
                                                    class="text-danger">*</span></label>
                                            <input type="time" id="clock_in"
                                                class="form-control @error('clock_in') is-invalid @enderror" name="clock_in"
                                                value="{{ old('clock_in', date('H:i')) }}" required>
                                            @error('clock_in')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <!-- Clock In Status -->
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="clock_in_status">Status Clock In <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select @error('clock_in_status') is-invalid @enderror"
                                                id="clock_in_status" name="clock_in_status" required>
                                                <option value="normal"
                                                    {{ old('clock_in_status', 'normal') == 'normal' ? 'selected' : '' }}>
                                                    Tepat Waktu
                                                </option>
                                                <option value="late"
                                                    {{ old('clock_in_status') == 'late' ? 'selected' : '' }}>
                                                    Terlambat
                                                </option>
                                            </select>
                                            @error('clock_in_status')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <!-- Clock In Image -->
                                        <div class="col-12 mb-3">
                                            <label class="form-label" for="clock_in_image">Foto Clock In <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="file"
                                                    class="form-control @error('clock_in_image') is-invalid @enderror"
                                                    id="clock_in_image" name="clock_in_image" accept="image/*" required>
                                                <button class="btn btn-outline-secondary" type="button"
                                                    id="capture_clock_in">
                                                    <i class="fas fa-camera"></i>
                                                </button>
                                            </div>
                                            @error('clock_in_image')
                                                <div class="invalid-feedback d-block">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                            <div class="mt-2">
                                                <img id="clock_in_preview" src="#" alt="Preview"
                                                    class="img-thumbnail" style="max-height: 200px; display: none;">
                                            </div>
                                        </div>

                                        <!-- Clock Out Toggle -->
                                        <div class="col-12 mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="include_clock_out"
                                                    name="include_clock_out"
                                                    {{ old('include_clock_out') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="include_clock_out">Tambahkan Data
                                                    Clock
                                                    Out</label>
                                            </div>
                                        </div>

                                        <!-- Clock Out Section -->
                                        <div id="clock_out_section" class="col-12" style="display: none;">
                                            <hr>
                                            <h5 class="mb-3">Data Clock Out</h5>

                                            <div class="row">
                                                <!-- Clock Out Time -->
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="clock_out">Waktu Clock Out <span
                                                            class="text-danger clock-out-required"
                                                            style="display: none;">*</span></label>
                                                    <input type="time" id="clock_out"
                                                        class="form-control @error('clock_out') is-invalid @enderror"
                                                        name="clock_out" value="{{ old('clock_out') }}">
                                                    @error('clock_out')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>

                                                <!-- Clock Out Status -->
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label" for="clock_out_status">Status Clock Out
                                                        <span class="text-danger clock-out-required"
                                                            style="display: none;">*</span></label>
                                                    <select
                                                        class="form-select @error('clock_out_status') is-invalid @enderror"
                                                        id="clock_out_status" name="clock_out_status">
                                                        <option value="normal"
                                                            {{ old('clock_out_status', 'normal') == 'normal' ? 'selected' : '' }}>
                                                            Tepat Waktu
                                                        </option>
                                                        <option value="early"
                                                            {{ old('clock_out_status') == 'early' ? 'selected' : '' }}>
                                                            Pulang Awal
                                                        </option>
                                                    </select>
                                                    @error('clock_out_status')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>

                                                <!-- Clock Out Image -->
                                                <div class="col-12 mb-3">
                                                    <label class="form-label" for="clock_out_image">Foto Clock Out <span
                                                            class="text-danger clock-out-required"
                                                            style="display: none;">*</span></label>
                                                    <div class="input-group">
                                                        <input type="file"
                                                            class="form-control @error('clock_out_image') is-invalid @enderror"
                                                            id="clock_out_image" name="clock_out_image" accept="image/*">
                                                        <button class="btn btn-outline-secondary" type="button"
                                                            id="capture_clock_out">
                                                            <i class="fas fa-camera"></i>
                                                        </button>
                                                    </div>
                                                    @error('clock_out_image')
                                                        <div class="invalid-feedback d-block">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                    <div class="mt-2">
                                                        <img id="clock_out_preview" src="#" alt="Preview"
                                                            class="img-thumbnail"
                                                            style="max-height: 200px; display: none;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Form Buttons -->
                                        <div class="col-12 d-flex justify-content-end mt-3">
                                            <a href="{{ route('attendance') }}"
                                                class="btn btn-secondary me-2">Kembali</a>
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </div>
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
    <!-- Select2 -->
    <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-select2.init.js') }}"></script>

    <script>
        const getEmployeeSchedulesUrl = "{{ route('attendance.getEmployeeSchedules') }}";

        $(document).ready(function() {
            // Enhanced select with search
            $('#employee_id, #work_schedule_id').select2({
                placeholder: 'Pilih opsi',
                width: '100%'
            });

            // Initialize date picker with today's date if not set
            if (!$('#attendance_date').val()) {
                $('#attendance_date').val(formatDate(new Date()));
            }

            // Toggle clock out section
            $('#include_clock_out').change(function() {
                toggleClockOutSection();
            });

            // Initial toggle state based on existing selection
            toggleClockOutSection();

            // Load work schedules when employee or date changes
            $('#employee_id, #attendance_date').change(function() {
                loadWorkSchedules();
            });

            // Initial load of work schedules if employee is selected
            if ($('#employee_id').val()) {
                loadWorkSchedules();
            }

            // Auto-determine clock in status based on schedule time
            $('#clock_in').change(function() {
                determineClockInStatus();
            });

            // Auto-determine clock out status based on schedule time
            $('#clock_out').change(function() {
                determineClockOutStatus();
            });

            $('#clock_in_image').change(function() {
                updateImagePreview('clock_in');
            });

            $('#clock_out_image').change(function() {
                updateImagePreview('clock_out');
            });

            // Camera capture functionality
            $('#capture_clock_in').click(function() {
                captureImage('clock_in');
            });

            $('#capture_clock_out').click(function() {
                captureImage('clock_out');
            });

            // Form validation
            $('#attendanceForm').submit(function(event) {
                if (!validateForm()) {
                    event.preventDefault();
                    return false;
                }
                return true;
            });

            // FUNCTIONS

            // Toggle clock out section visibility and required fields
            function toggleClockOutSection() {
                if ($('#include_clock_out').is(':checked')) {
                    $('#clock_out_section').slideDown();
                    $('#clock_out, #clock_out_status, #clock_out_image').prop('required', true);
                    $('.clock-out-required').show();
                } else {
                    $('#clock_out_section').slideUp();
                    $('#clock_out, #clock_out_status, #clock_out_image').prop('required', false);
                    $('.clock-out-required').hide();
                }
            }

            // Load work schedules for the selected employee and date
            function loadWorkSchedules() {
                const employeeId = $('#employee_id').val();
                const date = $('#attendance_date').val();

                if (!employeeId || !date) return;

                $('#schedule-loading').show();
                $('#no-schedule-message').hide();
                $('#work_schedule_id').empty().append('<option value="">Pilih Jadwal</option>');

                $.ajax({
                    url: getEmployeeSchedulesUrl,
                    type: "GET",
                    data: {
                        employee_id: employeeId,
                        date: date
                    },
                    success: function(response) {
                        $('#schedule-loading').hide();

                        if (response.success && response.data.length > 0) {
                            response.data.forEach(function(schedule) {
                                const shiftInfo = schedule.shift ?
                                    `${schedule.shift.name} (${schedule.shift.start_time} - ${schedule.shift.end_time})` :
                                    'Tidak Ada Informasi Shift';
                                $('#work_schedule_id').append(
                                    `<option value="${schedule.id}" 
                             data-start="${schedule.shift ? schedule.shift.start_time : ''}" 
                             data-end="${schedule.shift ? schedule.shift.end_time : ''}">${shiftInfo}</option>`
                                );
                            });

                            // Trigger change to update status fields
                            $('#work_schedule_id').trigger('change');
                        } else {
                            $('#no-schedule-message').show();
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#schedule-loading').hide();
                        $('#no-schedule-message').show();
                        console.error("Error loading schedules:", error);
                    }
                });
            }

            // Determine clock in status based on schedule time
            function determineClockInStatus() {
                const selectedSchedule = $('#work_schedule_id option:selected');
                if (selectedSchedule.val() && selectedSchedule.data('start')) {
                    const scheduleStart = selectedSchedule.data('start');
                    const clockIn = $('#clock_in').val();

                    if (clockIn && scheduleStart) {
                        // Convert to minutes since midnight for comparison
                        const scheduleMinutes = timeToMinutes(scheduleStart);
                        const clockInMinutes = timeToMinutes(clockIn);

                        // If clock in time is after schedule start time, mark as late
                        if (clockInMinutes > scheduleMinutes) {
                            $('#clock_in_status').val('late');
                        } else {
                            $('#clock_in_status').val('normal');
                        }
                    }
                }
            }

            // Determine clock out status based on schedule time
            function determineClockOutStatus() {
                const selectedSchedule = $('#work_schedule_id option:selected');
                if (selectedSchedule.val() && selectedSchedule.data('end')) {
                    const scheduleEnd = selectedSchedule.data('end');
                    const clockOut = $('#clock_out').val();

                    if (clockOut && scheduleEnd) {
                        // Convert to minutes since midnight for comparison
                        const scheduleMinutes = timeToMinutes(scheduleEnd);
                        const clockOutMinutes = timeToMinutes(clockOut);

                        // If clock out time is before schedule end time, mark as early
                        if (clockOutMinutes < scheduleMinutes) {
                            $('#clock_out_status').val('early');
                        } else {
                            $('#clock_out_status').val('normal');
                        }
                    }
                }
            }

            // Update image preview
            function updateImagePreview(type) {
                const fileInput = document.getElementById(`${type}_image`);
                const preview = document.getElementById(`${type}_preview`);

                if (fileInput.files && fileInput.files[0]) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    }

                    reader.readAsDataURL(fileInput.files[0]);
                } else {
                    preview.style.display = 'none';
                }
            }

            // Camera capture functionality
            // Camera capture functionality
            function captureImage(type) {
                // Create a camera modal if it doesn't exist
                if ($('#cameraModal').length === 0) {
                    $('body').append(`
    <div class="modal fade" id="cameraModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ambil Foto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <video id="cameraFeed" class="img-fluid" autoplay></video>
                        <canvas id="cameraCanvas" style="display:none;"></canvas>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="takePicture">Ambil Foto</button>
                </div>
            </div>
        </div>
    </div>
`);
                }

                // Store the current type for later use
                $('#cameraModal').data('type', type);

                // Show the modal
                const cameraModal = new bootstrap.Modal(document.getElementById('cameraModal'));
                cameraModal.show();

                // Access the camera
                const video = document.getElementById('cameraFeed');
                const canvas = document.getElementById('cameraCanvas');
                const context = canvas.getContext('2d');

                // Start the video stream
                navigator.mediaDevices.getUserMedia({
                        video: true
                    })
                    .then(function(stream) {
                        video.srcObject = stream;

                        // When the Take Picture button is pressed
                        $('#takePicture').off('click').on('click', function() {
                            // Set canvas dimensions to match video
                            canvas.width = video.videoWidth;
                            canvas.height = video.videoHeight;

                            // Draw the current video frame to the canvas
                            context.drawImage(video, 0, 0, canvas.width, canvas.height);

                            // Convert canvas to blob and create a file
                            canvas.toBlob(function(blob) {
                                const currentType = $('#cameraModal').data('type');
                                const fileInput = document.getElementById(
                                    `${currentType}_image`);

                                // Create a new File object
                                const file = new File([blob], `${currentType}_capture.jpg`, {
                                    type: 'image/jpeg'
                                });

                                // Create a DataTransfer to set the file input value
                                const dataTransfer = new DataTransfer();
                                dataTransfer.items.add(file);
                                fileInput.files = dataTransfer.files;

                                // Trigger change event to update preview
                                $(fileInput).trigger('change');

                                // Close the modal
                                cameraModal.hide();

                                // Stop the video stream
                                if (video.srcObject) {
                                    video.srcObject.getTracks().forEach(track => track.stop());
                                }
                            }, 'image/jpeg');
                        });

                        // When the modal is closed
                        $('#cameraModal').on('hidden.bs.modal', function() {
                            // Stop the video stream
                            if (video.srcObject) {
                                video.srcObject.getTracks().forEach(track => track.stop());
                            }
                        });
                    })
                    .catch(function(error) {
                        console.error("Error accessing camera:", error);
                        alert("Gagal mengakses kamera: " + error.message);
                        cameraModal.hide();
                    });
            }

            // Form validation
            function validateForm() {
                let isValid = true;
                const errorMessages = [];

                // Check if employee is selected
                if (!$('#employee_id').val()) {
                    errorMessages.push("Pilih karyawan");
                    isValid = false;
                }

                // Check clock in data
                if (!$('#clock_in').val()) {
                    errorMessages.push("Masukkan waktu clock in");
                    isValid = false;
                }

                // Check for clock in image file
                if (!$('#clock_in_image').prop('files') || !$('#clock_in_image').prop('files')[0]) {
                    errorMessages.push("Sediakan foto clock in");
                    isValid = false;
                }

                // Check clock out data if section is visible
                if ($('#include_clock_out').is(':checked')) {
                    if (!$('#clock_out').val()) {
                        errorMessages.push("Masukkan waktu clock out");
                        isValid = false;
                    }

                    // Check for clock out image file
                    if (!$('#clock_out_image').prop('files') || !$('#clock_out_image').prop('files')[0]) {
                        errorMessages.push("Sediakan foto clock out");
                        isValid = false;
                    }

                    // Check if clock out time is after clock in time
                    if ($('#clock_in').val() && $('#clock_out').val()) {
                        const clockInMinutes = timeToMinutes($('#clock_in').val());
                        const clockOutMinutes = timeToMinutes($('#clock_out').val());

                        if (clockOutMinutes <= clockInMinutes) {
                            errorMessages.push("Waktu clock out harus setelah waktu clock in");
                            isValid = false;
                        }
                    }
                }

                // Display error messages if any
                if (!isValid) {
                    alert("Mohon perbaiki kesalahan berikut:\n- " + errorMessages.join("\n- "));
                }

                return isValid;
            }

            // Helper function to convert time string to minutes since midnight
            function timeToMinutes(timeStr) {
                const [hours, minutes] = timeStr.split(':').map(Number);
                return hours * 60 + minutes;
            }

            // Helper function to format date as YYYY-MM-DD
            function formatDate(date) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            // When work schedule changes, update the clock in/out status
            $('#work_schedule_id').change(function() {
                determineClockInStatus();
                if ($('#include_clock_out').is(':checked')) {
                    determineClockOutStatus();
                }
            });
        });
    </script>
@endpush
