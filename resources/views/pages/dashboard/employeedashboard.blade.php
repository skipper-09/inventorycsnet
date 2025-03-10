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
            <div class="col-md-4">
                <a class="card shadow-sm" href="{{ route('assigmentdata') }}">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                                <i class="fas fa-calendar-alt text-info fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="card-title text-muted mb-0">Total Tugas</h6>
                                <h4 class="mb-0">{{ $totalTask ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm">
                    <a class="card-body" href="{{ route('leavereport') }}">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                                <i class="fas fa-calendar-alt text-info fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="card-title text-muted mb-0">Sisa Cuti</h6>
                                <h4 class="mb-0">{{ $remainingLeaves ?? 0 }} Hari</h4>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm">
                    <a class="card-body" href="{{ route('salary') }}">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                                <i class="fas fa-money-bill-wave text-warning fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="card-title text-muted mb-0">Gaji Bulan Ini</h6>
                                <h4 class="mb-0">Rp {{ number_format($netSalary, 0, ',', '.') }}</h4>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Leaves Section -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Pengajuan Cuti Terbaru</h5>
                    </div>
                    <div class="card-body">
                        @if(count($recentLeaves) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Tanggal Pengajuan</th>
                                            <th>Dari</th>
                                            <th>Sampai</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentLeaves as $leave)
                                            <tr>
                                                <td>{{ $leave->created_at_formatted }}</td>
                                                <td>{{ $leave->start_date_formatted }}</td>
                                                <td>{{ $leave->end_date_formatted }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $leave->status_color }}">
                                                        {{ ucfirst($leave->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-center text-muted my-3">Tidak ada pengajuan cuti terbaru</p>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Salary History Chart -->
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Riwayat Gaji</h5>
                    </div>
                    <div class="card-body">
                        <div style="height: 250px;">
                            <canvas id="salaryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                labels: {!! json_encode($salaryHistory->pluck('month')) !!},
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
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    </script>
@endpush