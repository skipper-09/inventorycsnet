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
                        <form action="{{ route('customer.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="mb-3">
                                    <label class="form-label w-100" for="branch_id">Piih Jalur</label>
                                    <select name="branch_id" id="branch_id" class="form-control select2form">
                                        <option value="">Pilih Cabang</option>
                                        @foreach ($branch as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="validationCustom01" class="form-label required">Nama Customer</label>
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
                                    <button class="btn btn-primary" type="button" id="get-location-btn">Get Lokasi
                                        Client</button>
                                </div>
                                <div class=" d-flex justify-between gap-3">
                                    <div class="mb-3">
                                        <label class="form-label" for="latitude">Latitude</label>
                                        <input type="text" id="latitude" name="latitude" class="form-control">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="longitude">Longitude</label>
                                        <input type="text" id="longitude" name="longitude" class="form-control">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="validationCustom01" class="form-label required">Alamat</label>
                                    <textarea name="address" class="form-control" cols="30"></textarea>
                                </div>

                                <hr class="text-bg-info">
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-primary btn-sm mb-3" id="addRow">Tambah
                                        Barang</button>
                                </div>

                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table class="table" id="myTable">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Barang</th>
                                                    <th>SN Modem</th>
                                                    <th>Jumlah</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
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

<!-- Required datatable js -->
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>

<!-- Responsive examples -->
<script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>
{{-- select 2 deifinition --}}
<script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/form-select2.init.js') }}"></script>

<script>
    $(document).ready(function() {
    $('#zone_id').on('change', function() {
        var zone_id = $(this).val();
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
            console.log(error.message)
            });
        } else {
            // alert('Geolocation is not supported by this browser.');
        }
    });

    $('#addRow').click(function() {
    const tableBody = $('#myTable tbody');
    const rowIndex = tableBody.children('tr').length + 1;
    const newRow = `
    <tr>
        <th scope="row">${rowIndex}</th>
        <td>
            <select name="item_id[]" class="form-control select2form">
                <option selected>Pilih Barang</option>
                @foreach ($product as $unit)
                    <option value="{{ $unit->id }}" data-name="{{ $unit->name }}">
                        {{ $unit->name }}
                    </option>
                @endforeach
            </select>
        </td>
        <td class="sn-modem-container" style="visibility: hidden;">
            <input type="text" name="sn_modem[]" class="form-control" value="">&nbsp;
        </td>
        <td>
            <input type="text" name="quantity[]" class="form-control" inputmode="numeric">
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm delete-btn">Delete</button>
        </td>
    </tr>`;
    tableBody.append(newRow);
    tableBody.find('.select2form').select2();

    // Bind event listener to newly added select elements
    tableBody.find('select[name="item_id[]"]').last().on('change', function() {
        toggleSnModemInput($(this));
    });

    // Check if the newly added row should show the sn_modem input
    toggleSnModemInput(tableBody.find('select[name="item_id[]"]').last());
    updateRowNumbers(); // Update row numbers after adding a row
});

// Function to toggle visibility of SN Modem input
function toggleSnModemInput(selectElement) {
    const selectedOption = selectElement.find('option:selected');
    const selectedText = selectedOption.data('name'); // Get the text of the selected option

    // Handle case when no option or "Pilih Barang" is selected
    if (!selectedText) {
        return; // Do nothing if no valid item is selected
    }

    // Find the sn_modem input field in the same row
    const snModemInputField = selectElement.closest('tr').find('.sn-modem-container');

    if (selectedText.toLowerCase() === 'modem') {
        snModemInputField.css('visibility', 'visible');
    } else {
        snModemInputField.css('visibility', 'hidden').find('input').val('');  // Hide and clear input value
    }
}

// Hapus baris
$('#myTable').on('click', '.delete-btn', function() {
    $(this).closest('tr').remove();
    updateRowNumbers(); // Update row numbers after deleting a row
});

// Update nomor urut baris
function updateRowNumbers() {
    $('#myTable tbody tr').each(function(index) {
        $(this).find('th').text(index + 1); // Update the number in the th
    });
}


});

</script>
@endpush