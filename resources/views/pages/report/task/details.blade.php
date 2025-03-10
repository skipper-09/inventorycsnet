@extends('layouts.base')

@section('title', $title)

@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    <style>
        /* Card styling */
        .card {
            border-radius: 0.5rem;
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .card-header {
            padding: 1rem 1.25rem;
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        }

        /* Task info boxes */
        .info-box {
            padding: 1rem;
            border-radius: 0.5rem;
            background-color: #f8f9fa;
            height: 100%;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }

        .info-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .info-box.primary {
            border-left-color: #38c66c;
        }

        .info-box.success {
            border-left-color: #41c3a9;
        }

        .info-box.warning {
            border-left-color: #ffd166;
        }

        .info-box.info {
            border-left-color: #4e7adf;
        }

        /* Progress bar */
        .progress {
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
        }

        /* Section headers */
        .section-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.25rem;
        }

        .section-icon {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background-color: #38c66c;
            color: white;
            margin-right: 0.75rem;
        }

        /* Accordion styling */
        .custom-accordion .accordion-item {
            border-radius: 0.5rem;
            overflow: hidden;
            margin-bottom: 0.75rem;
            border: 1px solid rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
        }

        .custom-accordion .accordion-button:not(.collapsed) {
            background-color: #d7f4e2;
            color: #38c66c;
            box-shadow: none;
        }

        .custom-accordion .accordion-button:focus {
            box-shadow: none;
        }

        /* Gallery images */
        .gallery-image {
            height: 160px;
            border-radius: 0.5rem;
            overflow: hidden;
            position: relative;
            margin-bottom: 1rem;
            transition: all 0.2s ease;
        }

        .gallery-image:hover {
            transform: translateY(-3px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .gallery-image img {
            object-fit: cover;
            width: 100%;
            height: 100%;
        }

        .gallery-label {
            position: absolute;
            top: 8px;
            left: 8px;
            padding: 0.25rem 0.5rem;
            border-radius: 30px;
            font-size: 0.7rem;
            font-weight: 600;
            z-index: 2;
        }

        .gallery-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 0.5rem;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0) 100%);
            color: white;
            opacity: 0;
            transition: all 0.2s ease;
        }

        .gallery-image:hover .gallery-overlay {
            opacity: 1;
        }

        /* Status badges */
        .badge-pill {
            padding: 0.35rem 0.75rem;
            border-radius: 30px;
            font-weight: 500;
            font-size: 0.75rem;
        }

        /* Dividers */
        .section-divider {
            margin-bottom: 1rem;
        }

        /* Animations */
        .fade-in {
            animation: fadeIn 0.4s ease-out forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Related tasks styling */
        .date-group-heading {
            padding: 0.5rem 1rem;
            background-color: #f8f9fa;
            border-left: 4px solid #38c66c;
            margin-bottom: 1rem;
            border-radius: 0 0.25rem 0.25rem 0;
        }

        .related-task-row {
            transition: all 0.2s ease;
        }

        .related-task-row:hover {
            background-color: #f8f9fa;
        }

        .current-task {
            background-color: rgba(56, 198, 108, 0.1);
            border-left: 3px solid #38c66c;
        }

        /* Print styles */
        @media print {
            .no-print {
                display: none !important;
            }

            .card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }

            .accordion-collapse {
                display: block !important;
            }

            .gallery-overlay {
                display: none !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <!-- Page Header -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">{{ $title }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('taskreport') }}">Task Report</a></li>
                            <li class="breadcrumb-item active">{{ $title }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Task Information Card -->
        <div class="card fade-in" style="animation-delay: 0.1s">
            <div class="card-header bg-white">
                <div class="d-flex align-items-center w-100">
                    <div class="d-flex align-items-center">
                        <span class="bg-primary rounded-circle me-3 text-white"
                            style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-tasks"></i>
                        </span>
                        <h5 class="mb-0 fw-bold">Informasi Tugas</h5>
                    </div>

                    {{-- <div class="ms-auto">
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                            data-bs-target="#reviewTaskModal">
                            <i class="fas fa-clipboard-check me-1"></i>Review Task
                        </button>
                    </div> --}}
                </div>
            </div>
            <div class="card-body p-3">
                <!-- Progress bar -->
                {{-- <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h6 class="mb-0 fw-bold">Progress Tugas</h6>
                        <small class="text-muted">Status penyelesaian</small>
                    </div>
                    <div class="text-end">
                        <h6 class="mb-0 fw-bold">{{ $progressPercentage }}%</h6>
                    </div>
                </div>
                <div class="progress mb-4">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progressPercentage }}%"
                        aria-valuenow="{{ $progressPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div> --}}

                <!-- Task info boxes -->
                <div class="row g-3">
                    <div class="col-md-6 col-lg-3">
                        <div class="info-box primary">
                            <h6 class="fw-bold mb-2"><i class="fas fa-clipboard-list me-2 text-primary"></i>Nama Tugas</h6>
                            <p class="mb-0">{{ $taskName }}</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="info-box warning">
                            <h6 class="fw-bold mb-2"><i class="fas fa-user me-2 text-warning"></i>Pemberi Tugas</h6>
                            <div class="d-flex align-items-center">
                                <span class="rounded-circle bg-warning text-white fw-bold me-2"
                                    style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">
                                    {{ isset($employeeTask->taskAssign->assigner->name)
                                        ? substr($employeeTask->taskAssign->assigner->name, 0, 1)
                                        : 'N' }}
                                </span>
                                <span>{{ $employeeTask->taskAssign->assigner->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="info-box info">
                            <h6 class="fw-bold mb-2"><i class="fas fa-user me-2 text-info"></i>Ditugaskan Kepada</h6>
                            <div class="d-flex align-items-center">
                                <span class="rounded-circle bg-info text-white fw-bold me-2"
                                    style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">
                                    {{ isset($employeeTask->employee->name) ? substr($employeeTask->employee->name, 0, 1) : 'N' }}
                                </span>
                                <span>{{ $employeeTask->employee->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="info-box warning">
                            <h6 class="fw-bold mb-2"><i class="fas fa-calendar me-2 text-warning"></i>Tanggal Penugasan</h6>
                            <p class="mb-0">{{ formatDate($taskAssign->assignment_date) }}</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="info-box success">
                            <h6 class="fw-bold mb-2"><i class="fas fa-map-marker-alt me-2 text-success"></i>Lokasi</h6>
                            <p class="mb-0">{{ $taskAssign->place ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Task Review -->
                @if ($taskReview->isNotEmpty())
                    <div class="mt-4">
                        <h5 class="mb-3 fw-bold">Review</h5>
                        @foreach ($taskReview as $log)
                            <div class="p-3 bg-light rounded mb-2">
                                {{ $log->log }}
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Related Tasks Section -->
        @if (isset($relatedTasks) && count($relatedTasks) > 0)
            {{-- <div class="section-header fade-in" style="animation-delay: 0.2s">
        <div class="section-icon">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <h5 class="fw-bold mb-0">Tugas Terkait Berdasarkan Tanggal</h5>
    </div> --}}

            <div class="card fade-in" style="animation-delay: 0.3s">
                <div class="card-header bg-white">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <div class="d-flex align-items-center">
                            <span class="bg-primary rounded-circle me-3 text-white"
                                style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-tasks"></i>
                            </span>
                            <h5 class="mb-0 fw-bold">{{ $taskName }}</h5>
                        </div>
                        <span class="badge-pill bg-primary text-white">
                            <i class="fas fa-list-ol ml-1"></i> {{ $relatedTasks->flatten()->count() }} Tugas
                        </span>
                    </div>
                </div>
                <div class="card-body p-3">

                    @foreach ($relatedTasks as $tasks)
                        <div class="accordion-item fade-in" style="animation-delay: {{ 0.3 + $loop->index * 0.1 }}s">

                            <div id="dateCollapse{{ $loop->index }}"
                                class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                aria-labelledby="dateHeading{{ $loop->index }}" data-bs-parent="#relatedTasksAccordion">
                                <div class="table-respons mt-3">
                                    <div class="accordion-body p-0">
                                        <table id="datatable"
                                            class="table table-hover table-bordered table-striped dt-responsive nowrap"
                                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Karyawan</th>
                                                    <th>Tugas</th>
                                                    <th>Nama Grup Tugas</th>
                                                    <th>Lokasi</th>
                                                    <th>Status</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($tasks as $task)
                                                    <tr
                                                        class="related-task-row {{ $task->id == $employeeTask->id ? 'current-task' : '' }}">
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <span class="rounded-circle bg-info text-white fw-bold me-2"
                                                                    style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-size: 0.7rem;">
                                                                    {{ isset($task->employee->name) ? substr($task->employee->name, 0, 1) : 'N' }}
                                                                </span>
                                                                <span>{{ $task->employee->name ?? 'N/A' }}</span>
                                                            </div>
                                                        </td>
                                                        <td>{{ $task->taskDetail->name ?? 'N/A' }}</td>
                                                        <td>{{ $task->taskDetail->task->name ?? 'N/A' }}</td>
                                                        <td>{{ $task->taskAssign->place ?? 'N/A' }}</td>
                                                        <td>
                                                            {!! $task->getStatus() !!}
                                                        </td>
                                                        <td>
                                                            <div class="d-flex gap-2">
                                                                <a href="{{ route('taskreport.details', ['id' => $task->id]) }}"
                                                                    class="btn btn-sm {{ $task->id == $employeeTask->id ? 'btn-secondary disabled' : 'btn-info' }}">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>

                                                                @if ($task->status != 'complated')
                                                                    <button type="button" class="btn btn-sm btn-primary"
                                                                        data-bs-toggle="modal"
                                                                        data-route="{{ route('taskreport.review', ['id' => $task->id]) }}"
                                                                        data-bs-target="#reviewTaskModal">
                                                                        <i class="fas fa-clipboard-check me-1"></i>Review
                                                                        Task
                                                                    </button>
                                                                @endif

                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        @endif

        <!-- Reports Section -->
        <div class="section-header fade-in" style="animation-delay: {{ isset($relatedTasks) ? '0.4s' : '0.2s' }}">
            <div class="section-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <h5 class="fw-bold mb-0">Laporan Tugas</h5>
        </div>

        <!-- Reports Accordion Card -->
        <div class="card fade-in" style="animation-delay: {{ isset($relatedTasks) ? '0.5s' : '0.3s' }}">
            <div class="card-header bg-white d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <span class="bg-primary rounded-circle me-3 text-white"
                        style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-clipboard-check"></i>
                    </span>
                    <h5 class="mb-0 fw-bold">Riwayat Laporan</h5>
                </div>
                <span class="badge-pill bg-primary text-white">
                    <i class="fas fa-list-ol me-1"></i> {{ count($taskReports) }} Laporan
                </span>
            </div>
            <div class="card-body p-3">
                @if (count($taskReports) > 0)
                    <div class="accordion custom-accordion" id="reportAccordion">
                        @foreach ($taskReports as $index => $report)
                            <div class="accordion-item fade-in"
                                style="animation-delay: {{ isset($relatedTasks) ? 0.5 + $index * 0.1 : 0.3 + $index * 0.1 }}s">
                                <h2 class="accordion-header" id="heading{{ $index }}">
                                    <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }}" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}"
                                        aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                                        aria-controls="collapse{{ $index }}">
                                        <div class="d-flex align-items-center justify-content-between w-100">
                                            <div class="d-flex align-items-center">
                                                <span class="rounded-circle bg-primary text-white me-2"
                                                    style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">
                                                    {{ $index + 1 }}
                                                </span>
                                                <div>
                                                    <span class="fw-bold">Laporan
                                                        {{ $report->employeeTask->taskDetail->name }}
                                                        #{{ $index + 1 }}</span>
                                                    <div class="d-flex align-items-center text-muted small">
                                                        <i class="far fa-calendar-alt me-1"></i>
                                                        <span>{{ formatDate($report->created_at) }}</span>
                                                        <span class="mx-1">•</span>
                                                        <i class="far fa-clock me-1"></i>
                                                        <span>{{ date('H:i', strtotime($report->created_at)) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex">
                                                @if (count($report->beforeImages) + count($report->afterImages) > 0)
                                                    <span class="badge-pill bg-info text-white me-2">
                                                        <i class="far fa-images me-1"></i>
                                                        {{ count($report->beforeImages) + count($report->afterImages) }}
                                                    </span>
                                                @endif
                                                <span
                                                    class="badge-pill {{ $report->reason_not_complated ? 'bg-warning' : 'bg-success' }} text-white me-2">
                                                    <i
                                                        class="fas {{ $report->reason_not_complated ? 'fa-hourglass-half' : 'fa-check-circle' }} me-1"></i>
                                                    {{ $report->reason_not_complated ? 'Belum Selesai' : 'Selesai' }}
                                                </span>
                                            </div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse{{ $index }}"
                                    class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                                    aria-labelledby="heading{{ $index }}" data-bs-parent="#reportAccordion">
                                    <div class="accordion-body">
                                        <!-- Report content -->
                                        <div class="mb-3">
                                            <div class="section-divider">
                                                <h6 class="fw-bold mb-2"><i class="fas fa-file-alt me-2"></i>Konten
                                                    Laporan</h6>
                                            </div>
                                            <div class="p-3 bg-light rounded">
                                                {{ $report->report_content }}
                                            </div>
                                        </div>

                                        <!-- Not completed reason -->
                                        @if ($report->reason_not_complated)
                                            <div class="mb-3">
                                                <div class="section-divider">
                                                    <h6 class="text-warning fw-bold mb-2"><i
                                                            class="fas fa-exclamation-triangle me-2"></i>Alasan Belum
                                                        Selesai</h6>
                                                </div>
                                                <div class="p-3 bg-light rounded">
                                                    {{ $report->reason_not_complated }}
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Images -->
                                        @if ($report->reportImage && count($report->reportImage) > 0)
                                            <div>
                                                <div class="section-divider">
                                                    <h6 class="fw-bold mb-2"><i class="far fa-images me-2"></i>Dokumentasi
                                                        Visual</h6>
                                                </div>

                                                <!-- Before Images -->
                                                @if (count($report->beforeImages) > 0)
                                                    <div class="mb-3">
                                                        <div class="badge-pill bg-primary text-white d-inline-block mb-2">
                                                            <i class="fas fa-camera me-1"></i> Kondisi Sebelum
                                                        </div>
                                                        <div class="row g-2">
                                                            @foreach ($report->beforeImages as $image)
                                                                <div class="col-lg-3 col-md-4 col-sm-6">
                                                                    <a href="{{ asset('storage/report/' . $image->image) }}"
                                                                        data-lightbox="report-before-{{ $report->id }}"
                                                                        data-title="Laporan #{{ $index + 1 }} - Sebelum {{ $loop->iteration }}">
                                                                        <div class="gallery-image">
                                                                            <span
                                                                                class="gallery-label bg-primary text-white">Sebelum</span>
                                                                            <img src="{{ asset('storage/report/' . $image->image) }}"
                                                                                alt="Before Image">
                                                                            <div class="gallery-overlay">
                                                                                <i class="fas fa-search-plus me-1"></i>
                                                                                Perbesar
                                                                            </div>
                                                                        </div>
                                                                    </a>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- After Images -->
                                                @if (count($report->afterImages) > 0)
                                                    <div>
                                                        <div class="badge-pill bg-success text-white d-inline-block mb-2">
                                                            <i class="fas fa-camera me-1"></i> Kondisi Sesudah
                                                        </div>
                                                        <div class="row g-2">
                                                            @foreach ($report->afterImages as $image)
                                                                <div class="col-lg-3 col-md-4 col-sm-6">
                                                                    <a href="{{ asset('storage/report/' . $image->image) }}"
                                                                        data-lightbox="report-after-{{ $report->id }}"
                                                                        data-title="Laporan #{{ $index + 1 }} - Sesudah {{ $loop->iteration }}">
                                                                        <div class="gallery-image">
                                                                            <span
                                                                                class="gallery-label bg-success text-white">Sesudah</span>
                                                                            <img src="{{ asset('storage/report/' . $image->image) }}"
                                                                                alt="After Image">
                                                                            <div class="gallery-overlay">
                                                                                <i class="fas fa-search-plus me-1"></i>
                                                                                Perbesar
                                                                            </div>
                                                                        </div>
                                                                    </a>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <div class="alert alert-info d-flex align-items-center">
                                                <i class="fas fa-info-circle me-2"></i>
                                                <span>Tidak ada gambar yang dilampirkan untuk laporan ini.</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-4 text-center">
                        <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                        <h5 class="fw-bold">Belum Ada Laporan</h5>
                        <p class="text-muted">Belum ada laporan yang dibuat untuk tugas ini.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @include('pages.report.task.review-form')
@endsection

@push('js')
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/datatables-base.init.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-select2.init.js') }}"></script>

    <script src="{{ asset('assets/js/mods/review.js') }}"></script>
    <script>
        @if (Session::has('message'))
            Swal.fire({
                title: `{{ Session::get('status') }}`,
                text: `{{ Session::get('message') }}`,
                icon: "{{ session('status') }}" === "Success!" ? "success" : "error",
                showConfirmButton: false,
                timer: 1500
            });
        @endif
    </script>
@endpush
