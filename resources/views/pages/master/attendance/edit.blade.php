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
                            <li class="breadcrumb-item active">Edit</li>
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
                            <h4 class="card-title">Form Edit Absensi</h4>
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

                            <form action="{{ route('attendance.update', $attendance->id) }}" method="POST"
                                class="form form-vertical" id="attendanceForm" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="form-body">
                                    <div class="row">
                                        <!-- Employee and Date Selection -->
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="employee_id">Karyawan <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select @error('employee_id') is-invalid @enderror"
                                                id="employee_id" name="employee_id" required>
                                                <option value="">Pilih Karyawan</option>
                                                @foreach ($employees as $employee)
                                                    <option value="{{ $employee->id }} "
                                                        {{ old('employee_id', $attendance->employee_id) == $employee->id ? 'selected' : '' }}>
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

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="attendance_date">Tanggal Absensi <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" id="attendance_date"
                                                class="form-control @error('attendance_date') is-invalid @enderror"
                                                name="attendance_date"
                                                value="{{ old('attendance_date', date('Y-m-d')) }}"
                                                required>
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
                                            <div id="schedule-info" class="mt-2">
                                                <div id="schedule-loading" class="text-muted" style="display: none;">
                                                    <i class="fas fa-spinner fa-spin"></i> Memuat jadwal...
                                                </div>
                                                <div id="no-schedule-message" class="text-muted" style="display: none;">
                                                    <i class="fas fa-info-circle"></i> Tidak ada jadwal untuk karyawan ini
                                                    pada tanggal tersebut. Absensi tetap dapat dibuat.
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Clock In Section -->
                                        <div class="col-12 mb-3">
                                            <div class="card bg-light border">
                                                <div class="card-header bg-light">
                                                    <h5 class="mb-0">Data Clock In</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <!-- Clock In Time -->
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label" for="clock_in">Waktu Clock In <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="time" id="clock_in"
                                                                class="form-control @error('clock_in') is-invalid @enderror"
                                                                name="clock_in"
                                                                value="{{ old('clock_in', $attendance->clock_in) }}"
                                                                required>
                                                            @error('clock_in')
                                                                <div class="invalid-feedback">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>

                                                        <!-- Clock In Status -->
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label" for="clock_in_status">Status Clock
                                                                In <span class="text-danger">*</span></label>
                                                            <select
                                                                class="form-select @error('clock_in_status') is-invalid @enderror"
                                                                id="clock_in_status" name="clock_in_status" required>
                                                                <option value="normal"
                                                                    {{ old('clock_in_status', $attendance->clock_in_status) == 'normal' ? 'selected' : '' }}>
                                                                    Tepat Waktu
                                                                </option>
                                                                <option value="late"
                                                                    {{ old('clock_in_status', $attendance->clock_in_status) == 'late' ? 'selected' : '' }}>
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
                                                        <div class="col-12">
                                                            <label class="form-label" for="clock_in_image">Foto Clock
                                                                In</label>
                                                            <input type="hidden" name="current_clock_in_image"
                                                                value="{{ $attendance->clock_in_image }}">
                                                            <div class="input-group">
                                                                <input type="file"
                                                                    class="form-control @error('clock_in_image') is-invalid @enderror"
                                                                    id="clock_in_image" name="clock_in_image"
                                                                    accept="image/*">
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

                                                            @if ($attendance->clock_in_image)
                                                                <div class="mt-2">
                                                                    <img id="clock_in_preview"
                                                                        src="{{ asset('storage/'. $attendance->clock_in_image) }}"
                                                                        alt="Preview" class="img-thumbnail"
                                                                        style="max-height: 200px;">
                                                                </div>
                                                            @else
                                                                <div class="mt-2">
                                                                    <img id="clock_in_preview" src=""
                                                                        alt="Preview" class="img-thumbnail"
                                                                        style="max-height: 200px; display: none;">
                                                                </div>
                                                            @endif
                                                            <small class="text-muted">Biarkan kosong jika tidak ingin
                                                                mengubah foto.</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Clock Out Toggle -->
                                        <div class="col-12 mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="include_clock_out"
                                                    name="include_clock_out"
                                                    {{ $attendance->clock_out ? 'checked' : '' }}>
                                                <label class="form-check-label" for="include_clock_out">Tambahkan Data
                                                    Clock Out</label>
                                            </div>
                                        </div>

                                        <!-- Clock Out Section -->
                                        <div id="clock_out_section" class="col-12"
                                            style="{{ $attendance->clock_out ? '' : 'display: none;' }}">
                                            <div class="card bg-light border">
                                                <div class="card-header bg-light">
                                                    <h5 class="mb-0">Data Clock Out</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <!-- Clock Out Time -->
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label" for="clock_out">Waktu Clock Out
                                                                <span class="text-danger clock-out-required"
                                                                    style="{{ $attendance->clock_out ? '' : 'display: none;' }}">*</span></label>
                                                            <input type="time" id="clock_out"
                                                                class="form-control @error('clock_out') is-invalid @enderror"
                                                                name="clock_out"
                                                                value="{{ old('clock_out', $attendance->clock_out) }}"
                                                                {{ $attendance->clock_out ? 'required' : '' }}>
                                                            @error('clock_out')
                                                                <div class="invalid-feedback">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>

                                                        <!-- Clock Out Status -->
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label" for="clock_out_status">Status Clock
                                                                Out
                                                                <span class="text-danger clock-out-required"
                                                                    style="{{ $attendance->clock_out ? '' : 'display: none;' }}">*</span></label>
                                                            <select
                                                                class="form-select @error('clock_out_status') is-invalid @enderror"
                                                                id="clock_out_status" name="clock_out_status"
                                                                {{ $attendance->clock_out ? 'required' : '' }}>
                                                                <option value="normal"
                                                                    {{ old('clock_out_status', $attendance->clock_out_status) == 'normal' ? 'selected' : '' }}>
                                                                    Tepat Waktu
                                                                </option>
                                                                <option value="early"
                                                                    {{ old('clock_out_status', $attendance->clock_out_status) == 'early' ? 'selected' : '' }}>
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
                                                        <div class="col-12">
                                                            <label class="form-label" for="clock_out_image">Foto Clock Out
                                                                <span class="text-danger clock-out-required"
                                                                    style="{{ $attendance->clock_out ? '' : 'display: none;' }}">*</span></label>
                                                            <input type="hidden" name="current_clock_out_image"
                                                                value="{{ $attendance->clock_out_image }}">
                                                            <div class="input-group">
                                                                <input type="file"
                                                                    class="form-control @error('clock_out_image') is-invalid @enderror"
                                                                    id="clock_out_image" name="clock_out_image"
                                                                    accept="image/*"
                                                                    {{ $attendance->clock_out && !$attendance->clock_out_image ? 'required' : '' }}>
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

                                                            @if ($attendance->clock_out_image)
                                                                <div class="mt-2">
                                                                    <img id="clock_out_preview"
                                                                        src="{{ asset('storage/'. $attendance->clock_out_image) }}"
                                                                        alt="Preview" class="img-thumbnail"
                                                                        style="max-height: 200px;">
                                                                </div>
                                                            @else
                                                                <div class="mt-2">
                                                                    <img id="clock_out_preview" src=""
                                                                        alt="Preview" class="img-thumbnail"
                                                                        style="max-height: 200px; display: none;">
                                                                </div>
                                                            @endif
                                                            <small class="text-muted">Biarkan kosong jika tidak ingin
                                                                mengubah foto.</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Additional Notes -->
                                        <div class="col-12 mt-3">
                                            <label class="form-label" for="notes">Catatan Tambahan</label>
                                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $attendance->notes) }}</textarea>
                                            @error('notes')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Form Actions -->
                                <div class="form-actions mt-4 text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                                    </button>
                                    <a href="{{ route('attendance') }}" class="btn btn-secondary ms-2">
                                        <i class="fas fa-times me-1"></i> Batal
                                    </a>
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

            // Load initial schedule if data exists
            if ($('#employee_id').val() && $('#attendance_date').val()) {
                loadEmployeeSchedule();
            }

            // Toggle Clock Out Section
            $('#include_clock_out').change(function() {
                if (this.checked) {
                    $('#clock_out_section').slideDown();
                    $('.clock-out-required').show();
                    $('#clock_out, #clock_out_status').attr('required', true);
                    // Only require image if no existing image
                    if (!$('#clock_out_preview').attr('src')) {
                        $('#clock_out_image').attr('required', true);
                    }
                } else {
                    $('#clock_out_section').slideUp();
                    $('.clock-out-required').hide();
                    $('#clock_out, #clock_out_status, #clock_out_image').removeAttr('required');
                }
            });

            // Handle Image Preview for Clock In
            $('#clock_in_image').change(function() {
                previewImage(this, '#clock_in_preview');
            });

            // Handle Image Preview for Clock Out
            $('#clock_out_image').change(function() {
                previewImage(this, '#clock_out_preview');
            });

            // Function to preview uploaded images
            function previewImage(input, previewElement) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $(previewElement).attr('src', e.target.result).show();
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            // Handle Work Schedule Loading
            $('#employee_id, #attendance_date').change(function() {
                loadEmployeeSchedule();
            });

            // Function to load employee schedule
            function loadEmployeeSchedule() {
                var employeeId = $('#employee_id').val();
                var attendanceDate = $('#attendance_date').val();

                if (employeeId && attendanceDate) {
                    $('#schedule-loading').show();
                    $('#no-schedule-message').hide();

                    $.ajax({
                        url: getEmployeeSchedulesUrl,
                        type: "GET",
                        data: {
                            employee_id: employeeId,
                            attendance_date: attendanceDate
                        },
                        success: function(response) {
                            $('#schedule-loading').hide();
                            if (response.schedule) {
                                $('#work_schedule_id').html('<option value="' + response
                                    .schedule.id + '" data-start="' + response.schedule
                                    .shift.start_time + '" data-end="' + response.schedule
                                    .shift.end_time + '" selected>' + response.schedule
                                    .shift.name + ' (' + response.schedule.shift
                                    .start_time + ' - ' + response.schedule.shift.end_time +
                                    ')</option>');
                            } else {
                                $('#no-schedule-message').show();
                                $('#work_schedule_id').html('<option value="">Tidak ada jadwal</option>');
                            }
                        },
                        error: function() {
                            $('#schedule-loading').hide();
                            $('#no-schedule-message').show();
                            $('#work_schedule_id').html('<option value="">Tidak ada jadwal</option>');
                        }
                    });
                }
            }
        });
    </script>
@endpush
