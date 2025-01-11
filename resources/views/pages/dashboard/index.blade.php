@extends('layouts.base')

@section('title',$title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="fs-16 fw-semibold mb-1 mb-md-2">Good Morning, <span class="text-primary">Jonas!</span>
                    </h4>
                    <p class="text-muted mb-0">Here's what's happening with your store today.</p>
                </div>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Clivax</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
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
                                    <p class="text-danger mb-1">Total balance</p>
                                    <h4 class="mb-0">$1,452.55</h4>
                                </div>
                            </div>
                            <div class="hstack gap-2 mt-3">
                                <button class="btn btn-light">Transfer</button>
                                <button class="btn btn-info">Request</button>
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
                                    <p class="text-success mb-1">Upcoming payments</p>
                                    <h4 class="mb-0">$120</h4>
                                </div>
                            </div>
                            <div class="mt-3 mb-2">
                                <p class="mb-0">4 not confirmed</p>
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
                                    <p class="text-info mb-1">Finished appt.</p>
                                    <h4 class="mb-0">72</h4>
                                </div>
                            </div>
                            <div class="mt-3 mb-2">
                                <p class="mb-0"><span class="text-primary me-2 fs-14"><i
                                            class="fas fa-caret-up me-1"></i>3.4%</span>vs last month</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->
            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Stok Barang Per-Cabang</h4>
                        </div><!-- end card header -->

                        <div class="card-body">
                            <div id="column_chart"
                                class="apex-charts" dir="ltr"></div>
                        </div><!-- end card-body -->
                    </div><!-- end card -->
                </div>
                <div class="col-xl-4">
                    <div class="card" style="overflow-y: auto; height: 304px;" data-simplebar="">
                        <div class="card-header card-header-bordered">
                            <div class="card-icon text-muted"><i class="fa fa-clipboard-list fs-14"></i></div>
                            <h3 class="card-title">Recent activities</h3>
                            <div class="card-addon">
                                <button class="btn btn-sm btn-label-primary">See all</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="timeline timeline-timed">
                                <div class="timeline-item">
                                    <span class="timeline-time">10:00</span>
                                    <div class="timeline-pin"><i class="marker marker-circle text-primary"></i></div>
                                    <div class="timeline-content">
                                        <div>
                                            <span>Meeting with</span>
                                            <div class="avatar-group ms-2">
                                                <div class="avatar avatar-circle">
                                                    <img src="{{ asset('assets/images/users/avatar-1.png') }}" alt="Avatar image"
                                                        class="avatar-2xs" />
                                                </div>
                                                <div class="avatar avatar-circle">
                                                    <img src="{{ asset('assets/images/users/avatar-2.png') }}" alt="Avatar image"
                                                        class="avatar-2xs" />
                                                </div>
                                                <div class="avatar avatar-circle">
                                                    <img src="{{ asset('assets/images/users/avatar-3.png') }}" alt="Avatar image"
                                                        class="avatar-2xs" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <span class="timeline-time">14:00</span>
                                    <div class="timeline-pin"><i class="marker marker-circle text-danger"></i></div>
                                    <div class="timeline-content">
                                        <p class="mb-0">Received a new feedback on <a href="#">GoFinance</a> App
                                            product.</p>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <span class="timeline-time">15:20</span>
                                    <div class="timeline-pin"><i class="marker marker-circle text-success"></i></div>
                                    <div class="timeline-content">
                                        <p class="mb-0">Lorem ipsum dolor sit amit,consectetur eiusmdd tempor incididunt
                                            ut labore et dolore magna.</p>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <span class="timeline-time">17:00</span>
                                    <div class="timeline-pin"><i class="marker marker-circle text-info"></i></div>
                                    <div class="timeline-content">
                                        <p class="mb-0">Make Deposit <a href="#">USD 700</a> o ESL.</p>
                                    </div>
                                </div>
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

@push('js')
<!-- apexcharts -->
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

<script>
    var productStocks = @json($productStocks); 
    var productNames = @json($productNames);
    var branchNames = @json($branchNames);

    var options = {
        chart: {
            type: 'bar',
            height: 350
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                endingShape: 'rounded'
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: productNames,
        },
        yaxis: {
            title: {
                text: 'Total Stock'
            }
        },
        fill: {
            opacity: 1
        },
        title: {
            text: 'Stok Barang',
            align: 'center'
        },
        series: productStocks.map(function(stock, index) {
            return {
                name: branchNames[index],
                data: stock
            };
        })
    };
    var chart = new ApexCharts(document.querySelector("#column_chart"), options);
    chart.render();
</script>


@endpush