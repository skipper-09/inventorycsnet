@extends('layouts.base')

@section('title', $title)

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
                            <li class="breadcrumb-item"><a href="{{ route('workschedule') }}">{{ $title }}</a></li>
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

                        <form action="{{ route('workschedule.store') }}" method="POST" class="form form-vertical">
                            @csrf
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="employee_id">Karyawan <span class="text-danger">*</span></label>
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
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="shift_id">Shift <span class="text-danger">*</span></label>
                                            <select class="form-select @error('shift_id') is-invalid @enderror"
                                                id="shift_id" name="shift_id" required>
                                                <option value="">Pilih Shift</option>
                                                @foreach ($shifts as $shift)
                                                    <option value="{{ $shift->id }}"
                                                        {{ old('shift_id') == $shift->id ? 'selected' : '' }}>
                                                        {{ $shift->name }} ({{ $shift->start_time }} -
                                                        {{ $shift->end_time }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('shift_id')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="schedule_date">Tanggal Jadwal <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" id="schedule_date"
                                                class="form-control @error('schedule_date') is-invalid @enderror"
                                                name="schedule_date" value="{{ old('schedule_date', date('Y-m-d')) }}"
                                                required>
                                            @error('schedule_date')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input @error('is_offday') is-invalid @enderror"
                                                    type="checkbox" id="is_offday" name="is_offday" value="1"
                                                    {{ old('is_offday') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_offday">Jadwalkan Sebagai Hari
                                                    Libur</label>
                                            </div>
                                            @error('is_offday')
                                                <div class="invalid-feedback d-block">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle"></i>
                                                Karyawan hanya diperbolehkan libur maksimal 4 kali dalam sebulan
                                            </small>
                                        </div>
                                    </div>

                                    <div class="col-12 d-flex justify-content-end mt-3">
                                        <a href="{{ route('workschedule') }}" class="btn btn-secondary me-2">Kembali</a>
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
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            // Enhanced select with search
            $('#employee_id, #shift_id').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });

            // Date validation - prevent selecting dates in the past
            $('#schedule_date').attr('min', new Date().toISOString().split('T')[0]);
        });
    </script>
@endpush
