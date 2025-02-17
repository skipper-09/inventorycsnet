@extends('layouts.base')

@section('title', $title)

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">{{ $title }}</h4>
                    <div class="page-title-right">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('salary') }}">Data Gaji</a></li>
                                <li class="breadcrumb-item active">{{ $title }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Employee Information -->
                        <div class="row mb-5">
                            <div class="col-md-6">
                                <h5 class="card-title mb-4">Informasi Karyawan</h5>
                                <div class="d-flex flex-column gap-3">
                                    <div class="d-flex">
                                        <div class="text-muted w-25">Nama Karyawan</div>
                                        <div>{{ $salary->employee->name }}</div>
                                    </div>
                                    <div class="d-flex">
                                        <div class="text-muted w-25">Departemen</div>
                                        <div>{{ $salary->employee->department->name }}</div>
                                    </div>
                                    <div class="d-flex">
                                        <div class="text-muted w-25">Posisi</div>
                                        <div>{{ $salary->employee->position->name }}</div>
                                    </div>
                                    <div class="d-flex">
                                        <div class="text-muted w-25">Periode Gaji</div>
                                        <div>{{ $formatted['salary_month'] }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5 class="card-title mb-4">Komponen Gaji</h5>
                                <canvas id="salaryChart" width="400" height="300"></canvas>
                            </div>
                        </div>

                        <!-- Salary Information -->
                        <div class="row mb-5">
                            <div class="col-12">
                                <h5 class="card-title mb-4">Detail Gaji</h5>
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <tr>
                                                <td class="text-muted" width="200">Gaji Pokok</td>
                                                <td width="30">:</td>
                                                <td class="fw-bold">{{ $formatted['basic_salary'] }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Bonus</td>
                                                <td>:</td>
                                                <td class="fw-bold">{{ $formatted['bonus'] }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Total Tunjangan</td>
                                                <td>:</td>
                                                <td class="fw-bold text-success">{{ $formatted['total_allowance'] }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Total Potongan</td>
                                                <td>:</td>
                                                <td class="fw-bold text-danger">{{ $formatted['total_deduction'] }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Gaji Bersih</td>
                                                <td>:</td>
                                                <td class="fw-bold fs-5">{{ $formatted['net_salary'] }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Allowances & Deductions -->
                        <div class="row mb-5">
                            <div class="col-md-6">
                                <h5 class="card-title mb-4">Rincian Tunjangan</h5>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Jenis Tunjangan</th>
                                                <th class="text-end">Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($grouped_allowances as $allowance)
                                                <tr>
                                                    <td>{{ $allowance['type'] }}</td>
                                                    <td class="text-end">Rp
                                                        {{ number_format($allowance['total'], 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5 class="card-title mb-4">Rincian Potongan</h5>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Jenis Potongan</th>
                                                <th class="text-end">Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($grouped_deductions as $deduction)
                                                <tr>
                                                    <td>{{ $deduction['type'] }}</td>
                                                    <td class="text-end">Rp
                                                        {{ number_format($deduction['total'], 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Salary History -->
                        <div class="row mb-5">
                            <div class="col-12">
                                <h5 class="card-title mb-4">Riwayat Gaji (6 Bulan Terakhir)</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Periode</th>
                                                <th class="text-end">Gaji Pokok</th>
                                                <th class="text-end">Bonus</th>
                                                <th class="text-end">Tunjangan</th>
                                                <th class="text-end">Potongan</th>
                                                <th class="text-end">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($salary_history as $history)
                                                <tr>
                                                    <td>{{ $history['month'] }}</td>
                                                    <td class="text-end">Rp
                                                        {{ number_format($history['basic_salary'], 0, ',', '.') }}</td>
                                                    <td class="text-end">Rp
                                                        {{ number_format($history['bonus'], 0, ',', '.') }}</td>
                                                    <td class="text-end">Rp
                                                        {{ number_format($history['allowance'], 0, ',', '.') }}</td>
                                                    <td class="text-end">Rp
                                                        {{ number_format($history['deduction'], 0, ',', '.') }}</td>
                                                    <td class="text-end fw-bold">Rp
                                                        {{ number_format($history['amount'], 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Back Button -->
                        <div class="row">
                            <div class="col-12">
                                <a href="{{ route('salary') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Kembali
                                </a>
                            </div>
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
        // Initialize salary components chart
        const ctx = document.getElementById('salaryChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Gaji Pokok', 'Bonus', 'Tunjangan', 'Potongan'],
                datasets: [{
                    data: [
                        {{ $statistics['basic_salary_percentage'] }},
                        {{ $statistics['bonus_percentage'] }},
                        {{ $statistics['allowance_percentage'] }},
                        {{ $statistics['deduction_percentage'] }}
                    ],
                    backgroundColor: [
                        '#4b77a9',
                        '#5f255f',
                        '#45b7af',
                        '#ff6384'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
@endpush
