@extends('layouts.base')

@section('title', 'Detail Absensi')

@section('content')
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('attendance') }}">Absensi</a></li>
                            <li class="breadcrumb-item active">Detail Absensi</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Detail Absensi</h4>
                </div>
            </div>
        </div>

        <!-- Card Details -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Informasi Absensi</h4>
                        <a href="{{ route('attendance') }}" class="btn btn-sm btn-primary">
                            <i class="mdi mdi-arrow-left"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nama Karyawan</label>
                                    <p>{{ $attendance->employee->name ?? 'N/A' }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nomor Induk Karyawan</label>
                                    <p>{{ $attendance->employee->employee_id ?? 'N/A' }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Jabatan</label>
                                    <p>{{ $attendance->employee->position->name ?? 'N/A' }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Departemen</label>
                                    <p>{{ $attendance->employee->department->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tanggal Absensi</label>
                                    <p>{{ \Carbon\Carbon::parse($attendance->created_at)->format('d F Y') }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Jadwal Kerja</label>
                                    <p>{{ $attendance->workSchedule->shift->name ?? 'N/A' }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Jam Masuk</label>
                                    <p>{{ $attendance->workSchedule->shift->start_time ?? 'N/A' }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Jam Keluar</label>
                                    <p>{{ $attendance->workSchedule->shift->end_time ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2">Detail Check-in & Check-out</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Waktu Check-in</label>
                                    <p>{{ $attendance->clock_in }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Status Check-in</label>
                                    <p>
                                        @if ($attendance->clock_in_status == 'late')
                                            <span class="badge bg-warning">Terlambat</span>
                                        @else
                                            <span class="badge bg-success">Tepat Waktu</span>
                                        @endif
                                    </p>
                                </div>

                                @if ($attendance->attendanceNotes->where('type', 'clock_in')->first())
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Catatan Check-in</label>
                                        <p>{{ $attendance->attendanceNotes->where('type', 'clock_in')->first()->notes }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Waktu Check-out</label>
                                    <p>{{ $attendance->clock_out ?? 'Belum Checkout' }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Status Check-out</label>
                                    <p>
                                        @if ($attendance->clock_out)
                                            @if ($attendance->clock_out_status == 'early')
                                                <span class="badge bg-warning">Pulang Awal</span>
                                            @else
                                                <span class="badge bg-success">Tepat Waktu</span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">Belum Checkout</span>
                                        @endif
                                    </p>
                                </div>

                                @if ($attendance->attendanceNotes->where('type', 'clock_out')->first())
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Catatan Check-out</label>
                                        <p>{{ $attendance->attendanceNotes->where('type', 'clock_out')->first()->notes }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if ($attendance->clock_in_image || $attendance->clock_out_image)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5 class="border-bottom pb-2">Foto Absensi</h5>
                                </div>
                                @if ($attendance->clock_in_image)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Foto Check-in</label>
                                        <div>
                                            <img src="{{ asset('storage/' . $attendance->clock_in_image) }}"
                                                alt="Foto Check-in" class="img-fluid rounded" style="max-height: 200px;">
                                        </div>
                                    </div>
                                @endif
                                @if ($attendance->clock_out_image)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Foto Check-out</label>
                                        <div>
                                            <img src="{{ asset('storage/' . $attendance->clock_out_image) }}"
                                                alt="Foto Check-out" class="img-fluid rounded" style="max-height: 200px;">
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="row mt-4">
                            <div class="col-12">
                                <h5 class="border-bottom pb-2">Durasi Kerja</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Total Jam Kerja</label>
                                    <p>
                                        @if ($attendance->clock_in && $attendance->clock_out)
                                            @php
                                                $clockIn = \Carbon\Carbon::parse($attendance->clock_in);
                                                $clockOut = \Carbon\Carbon::parse($attendance->clock_out);
                                                $diffInMinutes = $clockIn->diffInMinutes($clockOut); // Menghitung selisih dari clock_in ke clock_out
                                                $diffInHours = floor($diffInMinutes / 60); // Jam
                                                $remainingMinutes = $diffInMinutes % 60; // Sisa Menit
                                            @endphp
                                            {{ $diffInHours }} jam {{ $remainingMinutes }} menit
                                        @else
                                            Belum Tersedia
                                        @endif

                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-end">
                            @can('update-attendance')
                                <a href="{{ route('attendance.edit', $attendance->id) }}" class="btn btn-success me-2">
                                    <i class="mdi mdi-pencil"></i> Edit
                                </a>
                            @endcan
                            <a href="{{ route('attendance') }}" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
