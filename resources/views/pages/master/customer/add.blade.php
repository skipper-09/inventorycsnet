@extends('layouts.base')
@section('title', $title)

@push('css')
<!-- DataTables -->
<link href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet"
    type="text/css" />

<!-- Responsive datatable examples -->
<link href="{{ asset('assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet"
    type="text/css" />

{{-- select 2 --}}
<link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
@endpush
@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Tambah {{ $title }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('role') }}">{{ $title }}</a></li>
                        <li class="breadcrumb-item active">Tambah {{ $title }}</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->
</div>
<div class="container-fluid">
    <div class="page-content-wrapper">
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('role.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">

                                <div class="mb-3">
                                    <label for="validationCustom01" class="form-label required">Name</label>
                                    <input type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        id="validationCustom01">
                                    @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label w-100" for="zone_id">Piih Jalur</label>
                                    <select name="zone_id" id="zone_id" class="form-control select2form">
                                        <option value="">Pilih Jalur</option>
                                        @foreach ($zone as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label w-100" for="odp_id">Piih Odp</label>
                                    <select name="odp_id" id="odp_id" class="form-control select2form">
                                        <option value="">Pilih Odp</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="validationCustom01" class="form-label required">No HP</label>
                                    <input type="text" inputmode="numeric" name="phone"
                                        class="form-control @error('phone') is-invalid @enderror">
                                    @error('phone')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                
                                <div class="col-6 mb-2">
                                    <button class="btn btn-primary" type="button" id="get-location-btn">Get Lokasi Client</button>
                                </div>
                                <div class=" d-flex justify-between gap-3">
                                    <div class="mb-3">
                                        <label class="form-label" for="latitude">Latitude</label>
                                        <input type="text" id="latitude" name="latitude" class="form-control" readonly>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label" for="longitude">Longitude</label>
                                        <input type="text" id="longitude" name="longitude" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="validationCustom01" class="form-label required">Alamat</label>
                                   <textarea name="address" class="form-control" cols="30" ></textarea>
                                </div>
                            </div>
                            <div>
                                <button class="btn btn-primary" type="submit">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
{{--
<!-- Required datatable js -->
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>

<!-- Responsive examples -->
<script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script> --}}
{{-- select 2 deifinition --}}
<script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/form-select2.init.js') }}"></script>

<script>
    $(document).ready(function() {
    $('#zone_id').on('change', function() {
        var zone_id = $(this).val();
                console.log(zone_id);
                
        if (zone_id) {
            $.ajax({
                url: "{{ route('customer.getdataodp', ':zone_id') }}".replace(':zone_id', zone_id),
                type: 'GET',
                dataType: 'json',
                success: function(data) {

                    $('#odp_id').empty();
                    $('#odp_id').append('<option value="">Pilih Odp</option>');

                    $.each(data, function(key, value) {
                        $('#odp_id').append('<option value="'+ value.id +'">'+ value.name +'</option>');
                    });
                },
            });
        } else {
            $('#odp_id').empty();
            $('#odp_id').append('<option value="">Pilih Odp</option>');
        }
    });


    $('#get-location-btn').click(function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var latitude = position.coords.latitude;
                var longitude = position.coords.longitude;

                $('#latitude').val(latitude);
                $('#longitude').val(longitude);
            }, function(error) {
                alert('Error getting location: ' + error.message);
            });
        } else {
            alert('Geolocation is not supported by this browser.');
        }
    });
});

</script>
@endpush