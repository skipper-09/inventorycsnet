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
                        <div class="card-header">
                            <h3 class="card-title">{{ $title }}</h3>
                            <div class="card-addon">
                                <div class="nav nav-lines card-nav" id="card1-tab" role="tablist">
                                    <a class="nav-item nav-link active" id="card1-psb-tab" data-bs-toggle="tab"
                                        href="#card1-psb" aria-selected="true" role="tab">Pemasangan Baru</a>
                                    <a class="nav-item nav-link" id="card1-repair-tab" data-bs-toggle="tab"
                                        href="#card1-repair" aria-selected="false" role="tab"
                                        tabindex="-1">Perbaikan</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane fade active show" id="card1-psb" role="tabpanel"
                                    aria-labelledby="card1-psb-tab">
                                    @include('pages.report.customer.psbform')
                                </div>
                                <div class="tab-pane fade" id="card1-repair" role="tabpanel"
                                    aria-labelledby="card1-repair-tab">
                                    @include('pages.report.customer.repairform')
                                </div>
                            </div>
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
                        url: "{{ route('customer.getdataodp', ':zone_id') }}".replace(':zone_id',
                            zone_id),
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#odp_id').empty();
                            $('#odp_id').append('<option value="">Pilih Odp</option>');

                            if (data.length > 0) {
                                $.each(data, function(key, value) {
                                    $('#odp_id').append('<option value="' + value.name +
                                        '">' + value.name + '</option>');
                                });
                                $('#odp_id').prop('disabled', false);
                                $('#custom-odp-container').hide();
                            } else {
                                $('#odp_id').prop('disabled', true);
                                $('#custom-odp-container').show();
                            }
                        },
                    });
                } else {
                    $('#odp_id').empty();
                    $('#odp_id').append('<option value="">Pilih Odp</option>');
                    $('#odp_id').prop('disabled', true);
                    $('#custom-odp-container').hide();
                }
            });

            $('#custom_odp').on('input', function() {
                if ($(this).val() !== '') {
                    $('#odp_id').val('').prop('disabled', true);
                } else {
                    $('#odp_id').prop('disabled', false);
                }
            });


            $('form').on('submit', function(e) {
                var odp_id = $('#odp_id').val();
                var custom_odp = $('#custom_odp').val();

                if (odp_id) {
                    $('input[name="odp_id"]').val(odp_id);
                } else if (custom_odp) {
                    $('input[name="odp_id"]').val(custom_odp);
                }

                const itemIds = [];
                let isValid = true;
                let alertMessage = '';

                $('#myTable tbody tr').each(function() {
                    const itemId = $(this).find('select[name="item_id[]"]').val();
                    const quantity = $(this).find('input[name="quantity[]"]').val();
                    const snModem = $(this).find('input[name="sn_modem[]"]').val();

                    console.log('Row data:', {
                        itemId,
                        quantity,
                        snModem
                    });

                    // Check if the item is selected
                    if (!itemId || itemId === 'Pilih Barang') {
                        isValid = false;
                        alertMessage = 'Please select a product for all rows.';
                        return false; // Exit the loop
                    }

                    itemIds.push(itemId);

                    // Check if quantity is filled
                    if (!quantity || quantity === '') {
                        isValid = false;
                        alertMessage = 'Please enter a quantity for all rows.';
                        return false; // Exit the loop
                    }

                    // Check if SN modem input should be filled (if applicable)
                    const snModemContainer = $(this).find('.sn-modem-container');
                    if (snModemContainer.css('visibility') === 'visible' && (!snModem || snModem
                            .trim() === '')) {
                        isValid = false;
                        alertMessage = 'Please enter a serial number for the modem.';
                        return false; // Exit the loop
                    }
                });

                // Check for duplicates only if we've passed the initial validations
                if (isValid && itemIds.length > 0) {
                    const duplicates = itemIds.filter((item, index) => itemIds.indexOf(item) !== index);
                    if (duplicates.length > 0) {
                        isValid = false;
                        alertMessage =
                            'Some products are selected more than once. Please select different products.';
                    }
                }

                // If validation failed, show an alert and prevent form submission
                if (!isValid) {
                    e.preventDefault(); // Properly prevent form submission
                    alert(alertMessage);
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

            let selectedItems = [];

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

                updateItemDropdown();

                tableBody.find('select[name="item_id[]"]').last().on('change', function() {
                    toggleSnModemInput($(this));
                    updateItemDropdown();
                });

                toggleSnModemInput(tableBody.find('select[name="item_id[]"]').last());
                updateRowNumbers();
            });

            function toggleSnModemInput(selectElement) {
                const selectedOption = selectElement.find('option:selected');
                const selectedText = selectedOption.data('name');

                if (!selectedText) {
                    return;
                }

                const snModemInputField = selectElement.closest('tr').find('.sn-modem-container');

                if (selectedText.toLowerCase() === 'modem') {
                    snModemInputField.css('visibility', 'visible');
                } else {
                    snModemInputField.css('visibility', 'hidden').find('input').val('');
                }
            }

            // Hapus baris
            $('#myTable').on('click', '.delete-btn', function() {
                const itemId = $(this).closest('tr').find('select[name="item_id[]"]').val();
                selectedItems = selectedItems.filter(item => item !== itemId);
                $(this).closest('tr').remove();
                updateRowNumbers();
                updateItemDropdown();
            });

            // Update nomor urut baris
            function updateRowNumbers() {
                $('#myTable tbody tr').each(function(index) {
                    $(this).find('th').text(index + 1);
                });
            }

            function updateItemDropdown() {
                const usedItems = [];

                $('#myTable tbody tr').each(function() {
                    const selectedValue = $(this).find('select[name="item_id[]"]').val();
                    if (selectedValue && selectedValue !== 'Pilih Barang') {
                        usedItems.push(selectedValue);
                    }
                });

                $('#myTable tbody tr').each(function() {
                    const selectElement = $(this).find('select[name="item_id[]"]');
                    const selectedValue = selectElement.val();

                    selectElement.find('option').each(function() {
                        const optionValue = $(this).val();
                        if (usedItems.includes(optionValue) && optionValue !== selectedValue) {
                            $(this).prop('disabled', true);
                        } else {
                            $(this).prop('disabled', false);
                        }
                    });
                });

                selectedItems = [];
                $('#myTable tbody tr').each(function() {
                    const selectedValue = $(this).find('select[name="item_id[]"]').val();
                    if (selectedValue && selectedValue !== 'Pilih Barang') {
                        selectedItems.push(selectedValue);
                    }
                });
            }

            $('#submitButton').click(function(e) {
                const itemIds = [];

                // Collect all selected item_ids from the rows
                $('#myTable tbody tr').each(function() {
                    const itemId = $(this).find('select[name="item_id[]"]').val();
                    if (itemId && itemId !== 'Pilih Barang') {
                        itemIds.push(itemId);
                    }
                });

                // Check for duplicates in the selected item_ids
                const duplicates = itemIds.filter((item, index) => itemIds.indexOf(item) !== index);

                if (duplicates.length > 0) {
                    e.preventDefault(); // Prevent form submission
                    alert('Some products are selected more than once. Please select different products.');
                }
            });
        });
    </script>
@endpush
