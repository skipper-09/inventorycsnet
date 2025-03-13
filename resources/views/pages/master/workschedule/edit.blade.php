@extends('layouts.app')

@section('title', $title)

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>{{ $title }}</h3>
                    <p class="text-subtitle text-muted">Edit data jadwal kerja karyawan</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('workschedule.index') }}">Jadwal Kerja</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Edit Jadwal Kerja</h4>
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-{{ session('status') == 'Success!' ? 'success' : 'danger' }} alert-dismissible fade show"
                            role="alert">
                            {{ session('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('workschedule.update', ['id' => $workSchedule->id]) }}" method="POST"
                        class="form form-vertical">
                        @csrf
                        @method('PUT')
                        <div class="form-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="employee_id">Karyawan <span class="text-danger">*</span></label>
                                        <select class="form-select @error('employee_id') is-invalid @enderror"
                                            id="employee_id" name="employee_id" required>
                                            <option value="">Pilih Karyawan</option>
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->id }}"
                                                    {{ old('employee_id', $workSchedule->employee_id) == $employee->id ? 'selected' : '' }}>
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
                                    <div class="form-group">
                                        <label for="shift_id">Shift <span class="text-danger">*</span></label>
                                        <select class="form-select @error('shift_id') is-invalid @enderror" id="shift_id"
                                            name="shift_id" required>
                                            <option value="">Pilih Shift</option>
                                            @foreach ($shifts as $shift)
                                                <option value="{{ $shift->id }}"
                                                    {{ old('shift_id', $workSchedule->shift_id) == $shift->id ? 'selected' : '' }}>
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
                                    <div class="form-group">
                                        <label for="schedule_date">Tanggal Jadwal <span class="text-danger">*</span></label>
                                        <input type="date" id="schedule_date"
                                            class="form-control @error('schedule_date') is-invalid @enderror"
                                            name="schedule_date"
                                            value="{{ old('schedule_date', $workSchedule->schedule_date) }}" required>
                                        @error('schedule_date')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input @error('is_offday') is-invalid @enderror"
                                                type="checkbox" id="is_offday" name="is_offday" value="1"
                                                {{ old('is_offday', $isOffday) ? 'checked' : '' }}>
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
                                    <a href="{{ route('workschedule.index') }}" class="btn btn-secondary me-2">Kembali</a>
                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
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
        });
    </script>
@endpush
