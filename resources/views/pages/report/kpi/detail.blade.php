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
    {{-- <h1>Detail Tugas Karyawan: {{ $tasks[0]['employee_name'] }}</h1>
    <h4>Bulan: {{ $bulanTahun }}</h4> --}}


    <div class="card fade-in" style="animation-delay: 0.1s">
        <div class="card-header bg-white">
            <div class="w-100">
                <div class="d-flex align-items-center">
                    <span class="bg-primary rounded-circle me-3 text-white"
                        style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">
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


    <div class="card fade-in" style="animation-delay: 0.1s">
        <div class="card-header bg-white">
            <div class="w-100">
                <div class="d-flex align-items-center">
                    <span class="bg-primary rounded-circle me-3 text-white"
                        style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-tasks"></i>
                    </span>
                    <h5 class="mb-0 fw-bold">Tugas Karyawan</h5>
                </div>
                
            </div>
        </div>
        <div class="mt-3 card-body">
            @foreach ($tasks as $taskAssign)
            <div class="card mb-4 shadow-lg">
                <div class="card-header d-flex justify-content-between align-items-center bg-light border-bottom">
                    <h5 class="mb-0 text-dark">{{ $taskAssign['employee_name'] }} - <small class="text-muted">{{ $taskAssign['task_assign_date'] }}</small></h5>
                    <span class="badge bg-primary text-white">{{ $taskAssign['template'] }} - {{ $taskAssign['place'] }}</span>
                    <button class="btn btn-outline-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $taskAssign['task_assign_id'] }}" aria-expanded="false" aria-controls="collapse{{ $taskAssign['task_assign_id'] }}">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
                <div id="collapse{{ $taskAssign['task_assign_id'] }}" class="collapse" aria-labelledby="heading{{ $taskAssign['task_assign_id'] }}" data-bs-parent="#accordionExample">
                    <div class="card-body bg-light">
                        @foreach ($taskAssign['tasks'] as $taskGroup)
                        <div class="card mb-3 p-4 border-light shadow-sm bg-white">
                            <h5 class="fw-bold text-uppercase text-success mb-3">{{ $taskGroup['task_name'] }}</h5>
                            @foreach ($taskGroup['tasks'] as $task)
                            <div class="task-details mb-4">
                                <p><strong class="text-dark">Task Name:</strong> {{ $task['task_name'] }}</p>
                                <p><strong class="text-dark">Status:</strong> 
                                    {!! $task['status'] !!}
                                </p>
                                
                                @if (!empty($task['reports']))
                                <div class="reports mt-4">
                                    <h6 class="text-muted">Reports:</h6>
                                    @foreach ($task['reports'] as $report)
                                    <div class="report mb-3 p-3 bg-light rounded border">
                                        <p><strong>Laporan: </strong> {{ $report['report_content'] }}</p>
                                        {{-- <p><strong>Alasan: </strong> {{ $report['reason_not_complated'] }}</p> --}}
                                        
                                        @if (!empty($report['report_images']))
                                        <div class="report-images mt-4">
                                            <h6 class="text-muted mb-3">Images:</h6>
                                            <div class="row">
                                                
                                                @foreach ($report['report_images'] as $index => $image)
                                                <div class="col-md-6 col-lg-4 mb-4">
                                                    <div class="image-container">
                                                        <div class="image-label mb-2">
                                                            @if ($index == 0 )
                                                            <span class="badge bg-info text-white">Gambar Sebelum</span>
                                                            @elseif ($index == 1)
                                                            <span class="badge bg-success text-white">Gambar Sesudah</span>
                                                            @endif
                                                        </div>
                                                        <div class="image-box">
                                                            <img src="{{ asset('storage/report/'.$image) }}" alt="Report Image" class="img-fluid rounded shadow-sm" />
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
                                <p class="text-muted">Tidak Ada Laporan Untuk Tugas ini.</p>
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


  

    <a href="{{ route('kpi.employee') }}" class="btn btn-primary">Kembali</a>
</div>
@endsection