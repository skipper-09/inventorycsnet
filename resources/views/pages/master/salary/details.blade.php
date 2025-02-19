@extends('layouts.base')

@section('title', $title)

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
                                <li class="breadcrumb-item"><a href="{{ route('salary') }}" class="text-primary">Data
                                        Gaji</a></li>
                                <li class="breadcrumb-item active">{{ $title }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <!-- Employee Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4 fw-bold">Informasi Karyawan</h5>
                                        <div class="d-flex flex-column gap-3">
                                            <div class="d-flex">
                                                <div class="text-muted w-35">Nama Karyawan</div>
                                                <div class="fw-medium">: {{ $salary->employee->name }}</div>
                                            </div>
                                            <div class="d-flex">
                                                <div class="text-muted w-35">Departemen</div>
                                                <div class="fw-medium">: {{ $salary->employee->department->name }}</div>
                                            </div>
                                            <div class="d-flex">
                                                <div class="text-muted w-35">Posisi</div>
                                                <div class="fw-medium">: {{ $salary->employee->position->name }}</div>
                                            </div>
                                            <div class="d-flex">
                                                <div class="text-muted w-35">Periode Gaji</div>
                                                <div class="fw-medium">: {{ $formatted['salary_month'] }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4 fw-bold">Komponen Gaji</h5>
                                        <div style="height: 220px">
                                            <canvas id="salaryChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Salary Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4 fw-bold">Detail Gaji</h5>
                                        <div class="table-responsive">
                                            <table class="table table-borderless mb-0">
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
                                                        <td class="fw-bold text-success">
                                                            {{ $formatted['total_allowance'] }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted">Total Potongan</td>
                                                        <td>:</td>
                                                        <td class="fw-bold text-danger">{{ $formatted['total_deduction'] }}
                                                        </td>
                                                    </tr>
                                                    <tr class="border-top">
                                                        <td class="text-muted">Gaji Bersih</td>
                                                        <td>:</td>
                                                        <td class="fw-bold fs-5 text-primary">
                                                            {{ $formatted['net_salary'] }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Allowances & Deductions -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4 fw-bold">Rincian Tunjangan</h5>
                                        <div class="table-responsive">
                                            <table class="table table-striped mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Jenis Tunjangan</th>
                                                        <th class="text-end">Jumlah</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($grouped_allowances as $allowance)
                                                        <tr>
                                                            <td>{{ $allowance['type'] }}</td>
                                                            <td class="text-end fw-medium">Rp
                                                                {{ number_format($allowance['total'], 0, ',', '.') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4 fw-bold">Rincian Potongan</h5>
                                        <div class="table-responsive">
                                            <table class="table table-striped mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Jenis Potongan</th>
                                                        <th class="text-end">Jumlah</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($grouped_deductions as $deduction)
                                                        <tr>
                                                            <td>{{ $deduction['type'] }}</td>
                                                            <td class="text-end fw-medium">Rp
                                                                {{ number_format($deduction['total'], 0, ',', '.') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Back Button -->
                        <div class="row">
                            <div class="col-12">
                                <a href="{{ route('salary') }}" class="btn btn-secondary px-4">
                                    <i class="fas fa-arrow-left me-2"></i> Kembali
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
                        '#4361ee',
                        '#7209b7',
                        '#2ec4b6',
                        '#e63946'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
        });
    </script>
@endpush
