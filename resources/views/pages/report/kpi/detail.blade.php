@extends('layouts.base')
@section('title', $title)

@push('css')
    <style>
        .image-container {
            text-align: center;
        }

        .image-label {
            font-weight: bold;
        }

        .image-box img {
            max-height: 300px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .image-box img:hover {
            transform: scale(1.03);
        }

        .chart-card {
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
        }

        .chart-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
        }

        .stat-card {
            border-radius: 12px;
            padding: 22px;
            margin-bottom: 20px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
        }

        .stat-card .icon {
            font-size: 2.2rem;
        }

        .stat-card .stat-title {
            font-size: 1rem;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: 700;
        }

        .stat-card.completed {
            background: linear-gradient(145deg, rgba(40, 167, 69, 0.08), rgba(40, 167, 69, 0.15));
            border-left: 5px solid #28a745;
        }

        .stat-card.in-review {
            background: linear-gradient(145deg, rgba(255, 193, 7, 0.08), rgba(255, 193, 7, 0.15));
            border-left: 5px solid #ffc107;
        }

        .stat-card.not-worked {
            background: linear-gradient(145deg, rgba(220, 53, 69, 0.08), rgba(220, 53, 69, 0.15));
            border-left: 5px solid #dc3545;
        }

        /* Task card improvements */
        .task-card {
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.3s ease;
            margin-bottom: 20px;
            border: none;
        }

        .task-card:hover {
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .task-header {
            padding: 15px 20px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .task-group {
            border-radius: 8px;
            margin-bottom: 15px;
            background-color: #fff;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }

        .task-group:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .task-details {
            border-left: 4px solid #4e73df;
            padding-left: 15px;
            margin-bottom: 20px;
        }

        .report {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        /* Animations */
        .fade-in {
            animation: fadeIn 0.5s ease forwards;
            opacity: 0;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">{{ $title }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">{{ $title }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="card fade-in"
            style="animation-delay: 0.1s; border-radius: 12px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.08);">
            <div class="card-header bg-white">
                <div class="w-100">
                    <div class="d-flex align-items-center">
                        <span class="bg-primary rounded-circle me-3 text-white"
                            style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user"></i>
                        </span>
                        <h5 class="mb-0 fw-bold">Informasi Karyawan</h5>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th>Nama Karyawan</th>
                            <td>{{ $tasks[0]['employee_name'] }}</td>
                        </tr>
                        <tr>
                            <th>Jabatan Karyawan</th>
                            <td>{{ $tasks[0]['position']['position']['name'] }}</td>
                        </tr>
                        <tr>
                            <th>Departemen Karyawan</th>
                            <td>{{ $tasks[0]['position']['department']['name'] }}</td>
                        </tr>
                        <tr>
                            <th>Bulan</th>
                            <td>{{ $bulanTahun }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Chart Section - Refined with pastel colors -->
        <div class="row mt-4">
            <div class="col-lg-8">
                <div class="card fade-in" style="animation-delay: 0.2s; border-radius: 8px;">
                    <div class="card-header bg-white">
                        <div class="d-flex align-items-center">
                            <span class="bg-primary rounded-circle me-3 text-white"
                                style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-chart-pie"></i>
                            </span>
                            <h5 class="mb-0">Statistik Status Tugas</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="height: 300px">
                            <div id="taskStatusChart"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card mb-3 fade-in" style="animation-delay: 0.3s; border-radius: 8px;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted" style="font-size: 0.9rem;">Tugas Selesai</div>
                                <div style="font-size: 1.5rem; font-weight: 600;" id="completed-count">0</div>
                                <div class="small text-muted" id="completed-percent"></div>
                            </div>
                            <div class="text-success" style="font-size: 1.8rem;">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3 fade-in" style="animation-delay: 0.4s; border-radius: 8px;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted" style="font-size: 0.9rem;">Dalam Review</div>
                                <div style="font-size: 1.5rem; font-weight: 600;" id="review-count">0</div>
                                <div class="small text-muted" id="review-percent"></div>
                            </div>
                            <div class="text-warning" style="font-size: 1.8rem;">
                                <i class="fas fa-hourglass-half"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3 fade-in" style="animation-delay: 0.5s; border-radius: 8px;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted" style="font-size: 0.9rem;">Belum Dikerjakan</div>
                                <div style="font-size: 1.5rem; font-weight: 600;" id="not-worked-count">0</div>
                                <div class="small text-muted" id="not-worked-percent"></div>
                            </div>
                            <div class="text-danger" style="font-size: 1.8rem;">
                                <i class="fas fa-exclamation-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card fade-in mt-4"
            style="animation-delay: 0.6s; border-radius: 12px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.08);">
            <div class="card-header bg-white">
                <div class="w-100">
                    <div class="d-flex align-items-center">
                        <span class="bg-primary rounded-circle me-3 text-white"
                            style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-tasks"></i>
                        </span>
                        <h5 class="mb-0 fw-bold">Tugas Karyawan</h5>
                    </div>
                </div>
            </div>
            <div class="mt-3 card-body">
                @foreach ($tasks as $index => $taskAssign)
                    <div class="card mb-4 shadow-sm task-card fade-in"
                        style="animation-delay: {{ 0.7 + $index * 0.1 }}s; border: none; border-radius: 15px; overflow: hidden;">
                        <div class="card-header d-flex justify-content-between align-items-center bg-light task-header"
                            style="border-bottom: 2px solid rgba(0,0,0,0.05); padding: 18px 20px;">
                            <div>
                                <h5 class="mb-0 text-dark fw-bold">{{ $taskAssign['employee_name'] }}</h5>
                                <small class="text-muted"><i
                                        class="far fa-calendar-alt me-1"></i>{{ $taskAssign['task_assign_date'] }}</small>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary text-white px-3 py-2 rounded-pill me-3"
                                    style="font-size: 0.85rem; letter-spacing: 0.5px;">
                                    {{ $taskAssign['template'] }} - {{ $taskAssign['place'] }}
                                </span>
                                <button class="btn btn-sm btn-primary rounded-circle shadow-sm toggle-btn" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapse{{ $taskAssign['task_assign_id'] }}"
                                    aria-expanded="false" aria-controls="collapse{{ $taskAssign['task_assign_id'] }}">
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                            </div>
                        </div>
                        <div id="collapse{{ $taskAssign['task_assign_id'] }}" class="collapse"
                            aria-labelledby="heading{{ $taskAssign['task_assign_id'] }}"
                            data-bs-parent="#accordionExample">
                            <div class="card-body bg-white p-4">
                                @foreach ($taskAssign['tasks'] as $taskIndex => $taskGroup)
                                    <div class="task-group p-4 mb-4 fade-in"
                                        style="animation-delay: {{ 0.8 + $taskIndex * 0.1 }}s; border-radius: 12px; box-shadow: 0 3px 12px rgba(0,0,0,0.05);">
                                        <h5 class="fw-bold text-uppercase text-primary mb-3 border-bottom pb-2"
                                            style="letter-spacing: 0.5px;">
                                            <i class="fas fa-clipboard-list me-2"></i>{{ $taskGroup['task_name'] }}
                                        </h5>
                                        @foreach ($taskGroup['tasks'] as $task)
                                            <div class="task-details mb-4"
                                                style="border-left: 4px solid #4e73df; background-color: rgba(78, 115, 223, 0.03); padding: 15px; border-radius: 0 8px 8px 0;">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h6 class="fw-bold mb-0">{{ $task['task_name'] }}</h6>
                                                    <div>{!! $task['status'] !!}</div>
                                                </div>

                                                @if (!empty($task['reports']))
                                                    <div class="reports mt-4">
                                                        <h6 class="text-muted mb-3"><i
                                                                class="fas fa-file-alt me-2"></i>Laporan:</h6>
                                                        @foreach ($task['reports'] as $report)
                                                            <div class="report"
                                                                style="background-color: #f8f9fa; border-radius: 12px; padding: 18px; margin-bottom: 20px; border-left: 3px solid #4e73df;">
                                                                <p class="mb-3"><strong>Laporan: </strong>
                                                                    {{ $report['report_content'] }}</p>

                                                                @if (!empty($report['report_images']))
                                                                    <div class="report-images mt-4">
                                                                        <h6 class="text-muted mb-3"><i
                                                                                class="fas fa-camera me-2"></i>Dokumentasi:
                                                                        </h6>
                                                                        <div class="row">
                                                                            @foreach ($report['report_images'] as $index => $image)
                                                                                <div class="col-md-6 col-lg-4 mb-4">
                                                                                    <div class="image-container">
                                                                                        <div class="image-label mb-2">
                                                                                            @if ($index == 0)
                                                                                                <span
                                                                                                    class="badge bg-info text-white px-3 py-2 rounded-pill shadow-sm">
                                                                                                    <i
                                                                                                        class="fas fa-image me-1"></i>Gambar
                                                                                                    Sebelum
                                                                                                </span>
                                                                                            @elseif ($index == 1)
                                                                                                <span
                                                                                                    class="badge bg-success text-white px-3 py-2 rounded-pill shadow-sm">
                                                                                                    <i
                                                                                                        class="fas fa-check-circle me-1"></i>Gambar
                                                                                                    Sesudah
                                                                                                </span>
                                                                                            @endif
                                                                                        </div>
                                                                                        <div class="image-box">
                                                                                            <img src="{{ asset('storage/report/' . $image) }}"
                                                                                                alt="Report Image"
                                                                                                class="img-fluid rounded shadow"
                                                                                                style="max-height: 300px; width: 100%; object-fit: cover; transition: transform 0.4s ease;" />
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <p class="text-muted fst-italic mt-2 mb-0 p-3 bg-light rounded">
                                                        <i class="fas fa-info-circle me-2"></i>Tidak Ada Laporan Untuk
                                                        Tugas ini.
                                                    </p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <a href="{{ route('kpi.employee') }}" class="btn btn-primary btn-lg rounded-pill px-4 mb-4 shadow-sm">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>
@endsection

@push('js')
    <!-- apexcharts -->
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/libs/moment/moment.js') }}"></script>
    <!-- apexcharts init -->
    <script src="{{ asset('assets/js/pages/widgets.init.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Chart data
            const chartData = {!! json_encode($chartData) !!};

            // Calculate total and percentages
            const total = chartData.datasets[0].data.reduce((sum, value) => sum + value, 0);
            const percentages = chartData.datasets[0].data.map(value =>
                total > 0 ? Math.round((value / total) * 100) : 0
            );

            // Update stat cards with counts and percentages
            document.getElementById('completed-count').textContent = chartData.datasets[0].data[0];
            document.getElementById('completed-percent').textContent = `${percentages[0]}% dari total`;

            document.getElementById('review-count').textContent = chartData.datasets[0].data[1];
            document.getElementById('review-percent').textContent = `${percentages[1]}% dari total`;

            document.getElementById('not-worked-count').textContent = chartData.datasets[0].data[2];
            document.getElementById('not-worked-percent').textContent = `${percentages[2]}% dari total`;

            // Prepare data for ApexCharts
            const labels = chartData.labels;
            const series = chartData.datasets[0].data;

            // Pastel colors
            const colors = ['#41c3a9', '#ffd166', '#fe5b5b']; // Pastel green, yellow, red

            // Create ApexCharts donut chart with refined options
            const options = {
                series: series,
                chart: {
                    type: 'donut',
                    height: 300,
                    fontFamily: 'inherit',
                    animations: {
                        enabled: true,
                        speed: 500,
                    }
                },
                labels: labels,
                colors: colors,
                plotOptions: {
                    pie: {
                        donut: {
                            size: '60%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total',
                                    fontSize: '14px',
                                    fontWeight: 500,
                                    color: '#6c757d',
                                },
                                value: {
                                    show: true,
                                    fontSize: '20px',
                                    fontWeight: 600,
                                    color: '#495057',
                                }
                            }
                        }
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: 2,
                    colors: ['#fff']
                },
                legend: {
                    position: 'bottom',
                    fontSize: '13px',
                    fontWeight: 400,
                    markers: {
                        width: 12,
                        height: 12,
                        radius: 6
                    },
                    itemMargin: {
                        horizontal: 10,
                        vertical: 5
                    },
                    formatter: function(seriesName, opts) {
                        return seriesName + ': ' + opts.w.globals.series[opts.seriesIndex] +
                            ' (' + percentages[opts.seriesIndex] + '%)';
                    }
                },
                tooltip: {
                    enabled: true,
                    theme: 'light',
                    y: {
                        formatter: function(value, {
                            seriesIndex
                        }) {
                            return `${value} tugas (${percentages[seriesIndex]}%)`;
                        }
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            height: 250
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            const chart = new ApexCharts(document.querySelector("#taskStatusChart"), options);
            chart.render();

            // Add clicking functionality to expand/collapse task details
            document.querySelectorAll('.task-card .card-header').forEach(header => {
                header.style.cursor = 'pointer';
                header.addEventListener('click', function(e) {
                    if (!e.target.closest('button')) {
                        const collapseId = this.closest('.task-card').querySelector('.collapse').id;
                        const collapseEl = document.getElementById(collapseId);
                        const bsCollapse = new bootstrap.Collapse(collapseEl);

                        if (collapseEl.classList.contains('show')) {
                            bsCollapse.hide();
                        } else {
                            bsCollapse.show();
                        }
                    }
                });
            });
        });
    </script>
@endpush
