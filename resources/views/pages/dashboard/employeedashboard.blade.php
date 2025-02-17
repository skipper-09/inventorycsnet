@extends('layouts.base')

@section('title', $title)

@section('content')
    <div class="container-fluid">
        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-primary text-white shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-1">{{ $greeting }}, {{ $employee->name }}!</h4>
                                <p class="mb-0">{{ $employee->position->name }} -
                                    {{ $employee->department->name }}</p>
                            </div>
                            {{-- <div class="text-end">
                                <p class="mb-0 opacity-75">NIK: {{ $employee->nik }}</p>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                                <i class="fas fa-tasks text-success fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="card-title text-muted mb-0">Tugas Aktif</h6>
                                <h4 class="mb-0">{{ $activeTasks ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                                <i class="fas fa-calendar-alt text-info fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="card-title text-muted mb-0">Sisa Cuti</h6>
                                <h4 class="mb-0">{{ $remainingLeaves ?? 0 }} Hari</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                                <i class="fas fa-money-bill-wave text-warning fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="card-title text-muted mb-0">Gaji Bulan Ini</h6>
                                <h4 class="mb-0">Rp {{ number_format($currentSalary, 0, ',', '.') }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3">
                                <i class="fas fa-clock text-danger fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="card-title text-muted mb-0">Task Deadline</h6>
                                <h4 class="mb-0">{{ $upcomingDeadlines ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <!-- Current Tasks -->
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title mb-0">Tugas Aktif</h5>
                            <a href="#" class="btn btn-sm btn-primary">Lihat Semua</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Judul</th>
                                        <th>Status</th>
                                        <th>Deadline</th>
                                        <th>Progress</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentTasks as $task)
                                        <tr>
                                            <td>{{ $task->title }}</td>
                                            <td>
                                                <span class="badge bg-{{ $task->status_color }}">
                                                    {{ $task->status }}
                                                </span>
                                            </td>
                                            <td>{{ $task->deadline_formatted }}</td>
                                            <td>
                                                <div class="progress" style="height: 5px;">
                                                    <div class="progress-bar" role="progressbar"
                                                        style="width: {{ $task->progress }}%"
                                                        aria-valuenow="{{ $task->progress }}" aria-valuemin="0"
                                                        aria-valuemax="100">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Tidak ada tugas aktif</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leave Requests -->
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title mb-0">Pengajuan Cuti</h5>
                            <a href="#" class="btn btn-sm btn-primary">Ajukan Cuti</a>
                        </div>
                        <div class="leave-list">
                            @forelse($recentLeaves as $leave)
                                <div class="leave-item p-3 border rounded mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-{{ $leave->status_color }}">{{ $leave->status }}</span>
                                        <small class="text-muted">{{ $leave->created_at_formatted }}</small>
                                    </div>
                                    <p class="mb-1">{{ $leave->reason }}</p>
                                    <small class="text-muted">
                                        {{ $leave->start_date_formatted }} - {{ $leave->end_date_formatted }}
                                        ({{ $leave->duration }} hari)
                                    </small>
                                </div>
                            @empty
                                <div class="text-center text-muted">
                                    Tidak ada pengajuan cuti terbaru
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Riwayat Gaji (6 Bulan Terakhir)</h5>
                        <div style="height: 300px">
                            <canvas id="salaryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Salary history chart
        const ctx = document.getElementById('salaryChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($salaryHistory->pluck('month')) !!}, // Use 'month' instead of 'salary_month'
                datasets: [{
                    label: 'Gaji Bersih',
                    data: {!! json_encode($salaryHistory->pluck('amount')) !!},
                    borderColor: '#4361ee',
                    backgroundColor: 'rgba(67, 97, 238, 0.1)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID'); // Add "Rp" prefix
                            }
                        }
                    }
                }
            }
        });
    </script>
@endpush
