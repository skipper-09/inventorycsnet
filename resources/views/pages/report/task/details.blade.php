@extends('layouts.base')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 fw-bold">{{ $title }}</h4>
                    <div class="page-title-right">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb m-0 bg-transparent">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"
                                        class="text-primary">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('taskreport') }}" class="text-primary">Task
                                        Report</a></li>
                                <li class="breadcrumb-item active">{{ $title }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Task Information Card -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Informasi Tugas</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="fw-bold">Nama Tugas:</label>
                                    <p>{{ $taskName }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="fw-bold">Ditugaskan Kepada:</label>
                                    <p>{{ $employeeTask->employee->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="fw-bold">Tanggal Penugasan:</label>
                                    <p>{{ formatDate($taskAssign->assignment_date) }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="fw-bold">Lokasi:</label>
                                    <p>{{ $taskAssign->place ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reports Card -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">Laporan Tugas</h5>
                    </div>
                    <div class="card-body">
                        @if (count($taskReports) > 0)
                            <div class="accordion" id="reportAccordion">
                                @foreach ($taskReports as $index => $report)
                                    <div class="accordion-item border mb-3">
                                        <h2 class="accordion-header" id="heading{{ $index }}">
                                            <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }}"
                                                type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapse{{ $index }}"
                                                aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                                                aria-controls="collapse{{ $index }}">
                                                <strong>Laporan #{{ $index + 1 }}</strong> -
                                                {{ formatDate($report->created_at) }}
                                            </button>
                                        </h2>
                                        <div id="collapse{{ $index }}"
                                            class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                                            aria-labelledby="heading{{ $index }}" data-bs-parent="#reportAccordion">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    <div class="col-md-12 mb-3">
                                                        <label class="fw-bold">Konten Laporan:</label>
                                                        <div class="p-3 bg-light rounded">
                                                            {{ $report->report_content }}
                                                        </div>
                                                    </div>

                                                    @if ($report->reason_not_complated)
                                                        <div class="col-md-12 mb-3">
                                                            <label class="fw-bold">Alasan Belum Selesai:</label>
                                                            <div class="p-3 bg-light rounded">
                                                                {{ $report->reason_not_complated }}
                                                            </div>
                                                        </div>
                                                    @endif

                                                    @if ($report->reportImage && count($report->reportImage) > 0)
                                                        <div class="col-md-12">
                                                            <label class="fw-bold">Gambar Laporan:</label>

                                                            <!-- Before Images -->
                                                            @if (count($report->beforeImages) > 0)
                                                                <h6 class="mt-3 mb-2">Gambar Sebelum:</h6>
                                                                <div class="row mt-2">
                                                                    @foreach ($report->beforeImages as $image)
                                                                        <div class="col-md-3 col-sm-4 col-6 mb-3">
                                                                            <a href="{{ asset('storage/' . $image->image) }}"
                                                                                data-lightbox="report-before-{{ $report->id }}"
                                                                                data-title="Laporan #{{ $index + 1 }} - Gambar Sebelum {{ $loop->iteration }}">
                                                                                <div class="image-container border rounded position-relative"
                                                                                    style="height: 150px; overflow: hidden;">
                                                                                    <img src="{{ asset('storage/' . $image->image) }}"
                                                                                        alt="Before Image"
                                                                                        class="img-fluid w-100 h-100"
                                                                                        style="object-fit: cover;">
                                                                                    <div
                                                                                        class="position-absolute top-0 start-0 bg-primary text-white px-2 py-1 rounded-bottom-right">
                                                                                        <small>Sebelum</small>
                                                                                    </div>
                                                                                </div>
                                                                            </a>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endif

                                                            <!-- After Images -->
                                                            @if (count($report->afterImages) > 0)
                                                                <h6 class="mt-3 mb-2">Gambar Sesudah:</h6>
                                                                <div class="row mt-2">
                                                                    @foreach ($report->afterImages as $image)
                                                                        <div class="col-md-3 col-sm-4 col-6 mb-3">
                                                                            <a href="{{ asset('storage/' . $image->image) }}"
                                                                                data-lightbox="report-after-{{ $report->id }}"
                                                                                data-title="Laporan #{{ $index + 1 }} - Gambar Sesudah {{ $loop->iteration }}">
                                                                                <div class="image-container border rounded position-relative"
                                                                                    style="height: 150px; overflow: hidden;">
                                                                                    <img src="{{ asset('storage/' . $image->image) }}"
                                                                                        alt="After Image"
                                                                                        class="img-fluid w-100 h-100"
                                                                                        style="object-fit: cover;">
                                                                                    <div
                                                                                        class="position-absolute top-0 start-0 bg-success text-white px-2 py-1 rounded-bottom-right">
                                                                                        <small>Sesudah</small>
                                                                                    </div>
                                                                                </div>
                                                                            </a>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endif

                                                            @if (count($report->beforeImages) == 0 && count($report->afterImages) == 0)
                                                                <p class="text-muted">Tidak ada gambar yang dilampirkan.</p>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <div class="col-md-12">
                                                            <p class="text-muted">Tidak ada gambar yang dilampirkan.</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info">
                                Belum ada laporan untuk tugas ini.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Back button -->
                <div class="text-end mt-3">
                    <a href="{{ route('taskreport') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <!-- Add lightbox support for image gallery -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script>
        // Initialize lightbox
        lightbox.option({
            'resizeDuration': 200,
            'wrapAround': true,
            'albumLabel': "Gambar %1 dari %2"
        });
    </script>
@endpush
