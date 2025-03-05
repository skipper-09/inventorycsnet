@extends('layouts.base')

@section('title', $title)

@push('css')
    <style>
        .timeline-container {
            max-height: 400px;
            overflow-y: auto;
        }

        .timeline-item {
            position: relative;
        }

        .timeline-icon {
            flex-shrink: 0;
        }

        .timeline-content {
            width: calc(100% - 60px);
        }

        /* Ensure smooth scrollbar */
        .timeline-container::-webkit-scrollbar {
            width: 8px;
        }

        .timeline-container::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .timeline-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .timeline-container::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .task-link {
            text-decoration: none;
            position: relative;
            transition: all 0.3s ease;
            border-radius: 8px;
            overflow: hidden;
        }

        .task-link:hover {
            background-color: rgba(0, 123, 255, 0.05);
            transform: translateY(-5px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .task-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background-color: transparent;
            transition: background-color 0.3s ease;
        }

        .task-link:hover::before {
            background-color: var(--primary);
        }

        .task-link:active {
            transform: translateY(-2px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="fs-16 fw-semibold mb-1 mb-md-2">{{ $greeting }}, <span
                                class="text-primary text-uppercase">{{ Auth::user()->name }}</span>
                        </h4>
                        <p class="text-muted mb-0">Here's what's happening with your Inventory today.</p>
                    </div>
                </div>
            </div>
        </div>
        <!--    end row -->

        <div class="row">
            <div class="col-xxl-12">
                <div class="row">
                    <div class="col-xl-4">
                        <div class="card bg-danger-subtle"
                            style="background: url('{{ asset('assets/images/dashboard/dashboard-shape-1.png') }}'); background-repeat: no-repeat; background-position: bottom center;">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="avatar avatar-sm avatar-label-danger">
                                        <i class="mdi mdi-buffer mt-1"></i>
                                    </div>
                                    <div class="ms-3">
                                        <p class="text-danger mb-1">Total Cabang</p>
                                        <h4 class="mb-0">{{ $branch }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="card bg-success-subtle"
                            style="background: url('{{ asset('assets/images/dashboard/dashboard-shape-2.png') }}'); background-repeat: no-repeat; background-position: bottom center; ">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="avatar avatar-sm avatar-label-success">
                                        <i class="mdi mdi-cash-usd-outline mt-1"></i>
                                    </div>
                                    <div class="ms-3">
                                        <p class="text-success mb-1">Total Barang</p>
                                        <h4 class="mb-0">{{ $product }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="card bg-info-subtle"
                            style="background: url('{{ asset('assets/images/dashboard/dashboard-shape-3.png') }}'); background-repeat: no-repeat; background-position: bottom center; ">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="avatar avatar-sm avatar-label-info">
                                        <i class="mdi mdi-webhook mt-1"></i>
                                    </div>
                                    <div class="ms-3">
                                        <p class="text-info mb-1">Total User</p>
                                        <h4 class="mb-0">{{ $user }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->

                <!-- New Sections for Latest Tasks and Activity Logs -->
                <div class="row">
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title mb-0">Latest Task Reports</h4>
                            </div>
                            <div class="card-body">
                                <div class="timeline-container" style="max-height: 400px; overflow-y: auto;">
                                    @forelse($latestTaskReport as $task)
                                        <a class="timeline-item task-link d-flex align-items-center mb-3 p-2 border-bottom"
                                            href="{{ route('taskreport.details', ['id' => $task->id]) }}">
                                            <div class="timeline-icon me-3">
                                                <div
                                                    class="avatar avatar-sm 
                                                    @if ($task->status == 'completed') avatar-soft-success 
                                                    @elseif($task->status == 'pending') avatar-soft-warning 
                                                    @else avatar-soft-danger @endif">
                                                    <span class="avatar-initial rounded-circle">
                                                        {{ substr($task->employee->name ?? 'N', 0, 1) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="timeline-content flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <h6 class="mb-0">
                                                        {{ $task->employee->name ?? 'N/A' }}
                                                    </h6>
                                                    <small class="text-muted">
                                                        {{ formatDate($task->taskAssign->assign_date) }}
                                                    </small>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <p class="text-muted mb-0">
                                                        {{ $task->taskDetail->task->name ?? 'N/A' }} - {{ $task->taskDetail->name ?? 'N/A' }} 
                                                    </p>
                                                    <span
                                                        class="badge 
                                                        @if ($task->status == 'completed') bg-success 
                                                        @elseif($task->status == 'pending') bg-warning 
                                                        @else bg-danger @endif">
                                                        {{ $task->status ?? 'Unknown' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="text-center text-muted py-3">
                                            No recent tasks found
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title mb-0">Latest Activity Logs</h4>
                            </div>
                            <div class="card-body">
                                <div class="timeline-container">
                                    @forelse($latestActivityLog as $activity)
                                        <div class="timeline-item d-flex align-items-center mb-3 pb-3 border-bottom">
                                            <div class="timeline-icon me-3">
                                                <div class="avatar avatar-sm avatar-soft-primary">
                                                    <span class="avatar-initial rounded-circle">
                                                        {{ substr($activity->causer->name ?? 'S', 0, 1) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="timeline-content flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <h6 class="mb-0">
                                                        {{ $activity->causer->name ?? 'System' }}
                                                    </h6>
                                                    <small class="text-muted">
                                                        {{ $activity->created_at->diffForHumans() }}
                                                    </small>
                                                </div>
                                                <p class="text-muted mb-0">
                                                    {{ $activity->description }}
                                                </p>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center text-muted py-3">
                                            No recent activities found
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end container-fluid -->
@endsection
