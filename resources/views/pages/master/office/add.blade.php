@extends('layouts.base')
@section('title', $title)

@push('css')
<!-- Select2 CSS -->
<link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('assets/vendor/leaflet/leaflet.css') }}" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-geosearch@3.0.0/dist/geosearch.css" />
<style>
    #map {
        height: 500px;
        width: 100%;
    }
</style>
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
                        <li class="breadcrumb-item"><a href="{{ route('office') }}">{{ $title }}</a></li>
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
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('office.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="mb-3">
                                    <label class="form-label required">Pilih Perusahaan</label>
                                    <select name="company_id"
                                        class="form-control select2form @error('company_id') is-invalid @enderror">
                                        <option value="">Pilih perusahaan</option>
                                        @foreach ($company as $item)
                                        <option value="{{ $item->id }}" {{ old('item')==$item->name ? 'selected' : ''
                                            }}>
                                            {{ $item->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('company_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="name">Nama Kantor</label>
                                    <input class="form-control @error('name') is-invalid @enderror" type="text"
                                        name="name" id="name" placeholder="Nama Kantor" value="{{ $office->name }}">
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="address">Alamat</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" name="address"
                                        id="address" placeholder="Alamat Kantor"></textarea>
                                    @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="latitude">Latitude</label>
                                    <input class="form-control @error('lat') is-invalid @enderror" type="text" "
                                        name="lat" id="latitude" placeholder="Latitude">
                                    @error('lat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="longitude">Longitude</label>
                                    <input class="form-control @error('long') is-invalid @enderror" type="text" 
                                        name="long" id="longitude" placeholder="Longitude">
                                    @error('long')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="radius">Radius (in meters)</label>
                                    <input class="form-control  @error('radius') is-invalid @enderror" type="text"
                                        name="radius" id="radius" placeholder="Radius in meters">
                                    @error('radius')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="map">Pilih Lokasi</label>
                                    <div id="map" class="map-container"></div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <a href="{{ route('employee') }}" class="btn btn-secondary ms-2">Kembali</a>
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
<!-- Select2 JS -->
<script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/form-select2.init.js') }}"></script>
<script src="{{ asset('assets/vendor/leaflet/leaflet.js') }}"></script>

<script>
    
    
    var map = L.map('map', {
            minZoom: 5, 
            maxZoom: 40,
        }).setView([-8.240616784207118, 114.3551152576204], 10);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

    var marker = L.marker([-8.240616784207118, 114.3551152576204]).addTo(map);

    var circle = L.circle([-8.240616784207118, 114.3551152576204], {
        color: 'blue',
        fillColor: '#30f',
        fillOpacity: 0.3,
        radius: 100
    }).addTo(map);

    map.on('click', function(e) {
        var lat = e.latlng.lat;
        var lon = e.latlng.lng;
        marker.setLatLng(e.latlng);
        circle.setLatLng(e.latlng);
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lon;
    });


    document.getElementById('radius').addEventListener('input', function() {
        var radius = parseInt(this.value); 
        if (!isNaN(radius) && radius > 0) {
            circle.setRadius(radius);
        }
    });

</script>

@endpush