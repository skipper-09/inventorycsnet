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
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-primary">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('taskreport') }}" class="text-primary">Task Report</a></li>
                                <li class="breadcrumb-item active">{{ $title }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Employee and Task Information -->
        <div class="row mb-4">
            <div class="col-xl-3">
                <div class="card overflow-hidden">
                    <div class="card-body pt-5">
                        <div class="row align-items-end">
                            <div class="col-sm-12">
                                <div class="avatar-md mb-3 mt-n4">
                                    <img src="{{ asset('assets/images/users/avatar-1.png') }}" alt="Profile" class="img-fluid avatar-circle bg-light p-2 border-2 border-primary">
                                </div>
                                <h5 class="fs-16 mb-1">{{ $employee->name ?? 'N/A' }}</h5>
                                <p class="text-muted mb-0">{{ $employee->position->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-9">
                <div class="card">
                    <div class="card-header card-header-bordered">
                        <div class="card-icon">
                            <i class="fa fa-clipboard-list fs-17 text-muted"></i>
                        </div>
                        <h3 class="card-title">Assignment Details</h3>
                        <div class="card-addon">
                            {!! $statusBadge !!}
                        </div>
                    </div>
                    <div class="card-body py-4">
                        <div class="row">
                            <!-- Task Information -->
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="card h-100 border-left-info shadow-sm info-card">
                                    <div class="card-body">
                                        <h6 class="text-info text-uppercase mb-3"><i class="fas fa-tasks me-2"></i>Task</h6>
                                        <h5 class="mb-3 text-primary">{{ $task->name ?? 'N/A' }}</h5>
                                        <p class="mb-3 text-muted">{{ $task->description ?? 'No description available' }}</p>
                                        <div class="mt-3">
                                            <p class="mb-2">
                                                <i class="fas fa-tag text-secondary me-2"></i>
                                                <span class="font-weight-bold">Category:</span> {{ $task->category ?? 'N/A' }}
                                            </p>
                                            <p class="mb-0">
                                                <i class="fas fa-flag text-secondary me-2"></i>
                                                <span class="font-weight-bold">Priority:</span>
                                                <span class="badge badge-{{ $task->priority == 'High' ? 'danger' : ($task->priority == 'Medium' ? 'warning' : 'success') }}">
                                                    {{ $task->priority ?? 'N/A' }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Timeline Information -->
                            <div class="col-md-6">
                                <div class="card h-100 border-left-warning shadow-sm info-card">
                                    <div class="card-body">
                                        <h6 class="text-warning text-uppercase mb-3"><i class="fas fa-calendar-alt me-2"></i>Timeline</h6>
                                        <div class="timeline-item">
                                            <div class="d-flex">
                                                <div class="timeline-indicator bg-primary"></div>
                                                <div class="ml-3 mb-3">
                                                    <h6 class="m-0">Assignment Date</h6>
                                                    <p class="text-muted m-0">{{ formatDate($taskAssign->assignment_date ?? null) }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="timeline-item">
                                            <div class="d-flex">
                                                <div class="timeline-indicator bg-warning"></div>
                                                <div class="ml-3 mb-3">
                                                    <h6 class="m-0">Due Date</h6>
                                                    <p class="text-muted m-0">{{ formatDate($taskAssign->due_date ?? null) }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="timeline-item">
                                            <div class="d-flex">
                                                <div class="timeline-indicator {{ $employeeTask->status == 'completed' ? 'bg-success' : 'bg-info' }}"></div>
                                                <div class="ml-3">
                                                    <h6 class="m-0">Completion Date</h6>
                                                    <p class="text-muted m-0">
                                                        {{ $employeeTask->status == 'completed' ? formatDate($employeeTask->completion_date ?? null) : 'Not completed yet' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="progress mt-3" style="height: 8px; border-radius: 4px;">
                                            @php
                                                $progressPercentage = 0;
                                                if ($employeeTask->status == 'completed') {
                                                    $progressPercentage = 100;
                                                } elseif ($employeeTask->status == 'in_progress') {
                                                    $progressPercentage = 50;
                                                } elseif ($employeeTask->status == 'assigned') {
                                                    $progressPercentage = 25;
                                                }
                                            @endphp
                                            <div class="progress-bar bg-{{ $employeeTask->status == 'completed' ? 'success' : ($employeeTask->status == 'in_progress' ? 'warning' : 'info') }} progress-bar-striped progress-bar-animated" 
                                                role="progressbar"
                                                style="width: {{ $progressPercentage }}%"
                                                aria-valuenow="{{ $progressPercentage }}" aria-valuemin="0"
                                                aria-valuemax="100"></div>
                                        </div>
                                        <small class="text-muted">Task Progress: {{ $progressPercentage }}% ({{ ucfirst($employeeTask->status) }})</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Details Section -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header card-header-bordered">
                        <div class="card-icon">
                            <i class="fas fa-file-alt fs-17 text-muted"></i>
                        </div>
                        <h3 class="card-title">Report Details</h3>
                    </div>
                    <div class="card-body">
                        <!-- Before Report -->
                        <div class="mb-5">
                            <h5 class="mb-3">
                                <i class="fas fa-file-upload text-primary me-2"></i> Before Report
                            </h5>
                            @if($beforeReport)
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="text-muted font-weight-bold">Report Date</label>
                                            <div class="p-3 bg-light rounded shadow-sm">
                                                {{ formatDate($beforeReport->created_at) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="text-muted font-weight-bold">Report Content</label>
                                            <div class="p-4 bg-light rounded shadow-sm" style="min-height: 200px;">
                                                {!! nl2br(e($beforeReport->report_content)) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if($beforeReport->report_image)
                                    <div class="mt-3">
                                        <h5 class="mb-3">Before Image</h5>
                                        <div class="card bg-light border-0 shadow-sm">
                                            <div class="card-body p-0">
                                                <img src="{{ asset('storage/' . $beforeReport->report_image) }}"
                                                    alt="Before Image" class="img-fluid rounded"
                                                    style="max-height: 350px; width: 100%; object-fit: cover;">
                                            </div>
                                            <div class="card-footer text-muted bg-transparent">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span><i class="fas fa-calendar me-1"></i> Uploaded on
                                                        <strong>{{ formatDate($beforeReport->created_at) }}</strong></span>
                                                    <div>
                                                        <a href="{{ asset('storage/' . $beforeReport->report_image) }}"
                                                            class="btn btn-sm btn-outline-primary" target="_blank">
                                                            <i class="fas fa-search-plus"></i>
                                                        </a>
                                                        <a href="{{ asset('storage/' . $beforeReport->report_image) }}"
                                                            class="btn btn-sm btn-outline-secondary ml-1" download>
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-warning shadow-sm">
                                    <i class="fas fa-exclamation-circle me-2"></i> No before report available.
                                </div>
                            @endif
                        </div>

                        <!-- After Report -->
                        <div>
                            <h5 class="mb-3">
                                <i class="fas fa-file-download text-success me-2"></i> After Report
                            </h5>
                            @if($afterReport)
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="text-muted font-weight-bold">Report Date</label>
                                            <div class="p-3 bg-light rounded shadow-sm">
                                                {{ formatDate($afterReport->created_at) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="text-muted font-weight-bold">Report Content</label>
                                            <div class="p-4 bg-light rounded shadow-sm" style="min-height: 200px;">
                                                {!! nl2br(e($afterReport->report_content)) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if($afterReport->report_image)
                                    <div class="mt-3">
                                        <h5 class="mb-3">After Image</h5>
                                        <div class="card bg-light border-0 shadow-sm">
                                            <div class="card-body p-0">
                                                <img src="{{ asset('storage/' . $afterReport->report_image) }}"
                                                    alt="After Image" class="img-fluid rounded"
                                                    style="max-height: 350px; width: 100%; object-fit: cover;">
                                            </div>
                                            <div class="card-footer text-muted bg-transparent">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span><i class="fas fa-calendar me-1"></i> Uploaded on
                                                        <strong>{{ formatDate($afterReport->created_at) }}</strong></span>
                                                    <div>
                                                        <a href="{{ asset('storage/' . $afterReport->report_image) }}"
                                                            class="btn btn-sm btn-outline-primary" target="_blank">
                                                            <i class="fas fa-search-plus"></i>
                                                        </a>
                                                        <a href="{{ asset('storage/' . $afterReport->report_image) }}"
                                                            class="btn btn-sm btn-outline-secondary ml-1" download>
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-warning shadow-sm">
                                    <i class="fas fa-exclamation-circle me-2"></i> No after report available.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection