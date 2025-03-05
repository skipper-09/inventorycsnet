@extends('layouts.base')

@section('title', $title)
@push('css')
    <style>
        .timeline-container {
            max-height: 400px;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }

        /* Scrollbar styles */
        .timeline-container::-webkit-scrollbar,
        .table-responsive::-webkit-scrollbar {
            width: 8px;
        }

        .timeline-container::-webkit-scrollbar-track,
        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .timeline-container::-webkit-scrollbar-thumb,
        .table-responsive::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .timeline-container::-webkit-scrollbar-thumb:hover,
        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Interaction styles */
        .task-link {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .task-link:hover {
            background-color: rgba(0, 123, 255, 0.05);
            transform: translateY(-3px);
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
            </div>
        </div>
        <!-- end row -->

        <div class="row">
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Latest Task Reports</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive timeline-container">
                            <table class="table table-hover mb-0">
                                <thead class="sticky-top bg-white">
                                    <tr>
                                        <th>Employee</th>
                                        <th>Task</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($latestTaskReport as $task)
                                        <tr class="task-link"
                                            onclick="window.location='{{ route('taskreport.details', ['id' => $task->id]) }}'">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div
                                                        class="avatar avatar-sm 
                                                        @if ($task->status == 'completed') avatar-soft-success 
                                                        @elseif($task->status == 'pending') avatar-soft-warning 
                                                        @else avatar-soft-danger @endif me-2">
                                                        <span class="avatar-initial rounded-circle">
                                                            {{ substr($task->employee->name ?? 'N', 0, 1) }}
                                                        </span>
                                                    </div>
                                                    {{ $task->employee->name ?? 'N/A' }}
                                                </div>
                                            </td>
                                            <td>
                                                {{ $task->taskDetail->task->name ?? 'N/A' }} -
                                                {{ $task->taskDetail->name ?? 'N/A' }}
                                            </td>
                                            <td>{{ formatDate($task->taskAssign->assign_date) }}</td>
                                            <td>
                                                <span
                                                    class="badge 
                                                    @if ($task->status == 'completed') bg-success 
                                                    @elseif($task->status == 'pending') bg-warning 
                                                    @else bg-danger @endif">
                                                    {{ $task->status ?? 'Unknown' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-3">
                                                No recent tasks found
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
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
                        <div class="timeline-container" style="max-height: 400px; overflow-y: auto;">
                            @forelse($latestActivityLog as $activity)
                                <div class="activity-log d-flex align-items-start mb-3 pb-3 border-bottom">
                                    <div class="me-3">
                                        <div class="avatar avatar-sm">
                                            <span class="avatar-initial rounded-circle">
                                                {{ substr($activity->causer->name ?? 'S', 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h6 class="mb-0">
                                                {{ $activity->causer->name ?? 'System' }}
                                                <span
                                                    class="badge ms-2
                                                    @if ($activity->event === 'created') bg-success
                                                    @elseif ($activity->event === 'updated') bg-warning
                                                    @elseif ($activity->event === 'deleted') bg-danger
                                                    @else bg-primary @endif text-xs">
                                                    {{ ucfirst($activity->event) }}
                                                </span>
                                            </h6>
                                            <small class="text-muted">
                                                {{ $activity->created_at->diffForHumans() }}
                                            </small>
                                        </div>

                                        <p class="text-muted mb-1">
                                            {{ $activity->description }}
                                        </p>

                                        @php
                                            $properties = is_string($activity->properties)
                                                ? json_decode($activity->properties, true)
                                                : $activity->properties;
                                        @endphp
                                        @if (is_array($properties))
                                            <div class="small text-muted">
                                                @foreach ($properties as $key => $value)
                                                    @if (!is_array($value))
                                                        <div>{{ ucfirst($key) }}: {{ $value }}</div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
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
@endsection
