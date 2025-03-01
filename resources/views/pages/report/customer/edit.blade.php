@extends('layouts.base')
@section('title', $title)

@push('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />

    <!-- Responsive datatable examples -->
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />

    {{-- Select2 --}}
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Edit {{ $title }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('customer') }}">{{ $title }}</a></li>
                            <li class="breadcrumb-item active">Edit {{ $title }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- End page title -->
    </div>

    <div class="container-fluid">
        <div class="page-content-wrapper">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('customer.update', ['id' => $customer->id]) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="mb-3">
                                        <label class="form-label w-100" for="branch_id">Pilih Cabang</label>
                                        <select name="branch_id" id="branch_id"
                                            class="form-control select2form @error('branch_id') is-invalid @enderror">
                                            <option value="">Pilih Cabang</option>
                                            @foreach ($branch as $item)
                                                <option value="{{ $item->id }}"
                                                    {{ $item->id == $customer->branch_id ? 'selected' : '' }}>
                                                    {{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('branch_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="validationCustom01" class="form-label required">Nama Customer</label>
                                        <input type="text" name="name" value="{{ $customer->name }}"
                                            class="form-control @error('name') is-invalid @enderror"
                                            id="validationCustom01">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label w-100" for="zone_id">Pilih Jalur</label>
                                        <select name="zone_id" id="zone_id"
                                            class="form-control select2form @error('zone_id') is-invalid @enderror">
                                            <option value="">Pilih Jalur</option>
                                            @foreach ($zone as $item)
                                                <option value="{{ $item->id }}"
                                                    {{ $item->id == $customer->zone_id ? 'selected' : '' }}>
                                                    {{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('zone_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label w-100" for="odp_id">Pilih Odp</label>
                                        <select name="odp_id" id="odp_id" class="form-control select2form">
                                            <option value="">Pilih Odp</option>
                                        </select>

                                        <div id="custom-odp-container" class="mt-2" style="display:none;">
                                            <label for="custom_odp" class="form-label">Masukkan ODP (Bila Tidak Ada
                                                ODP)</label>
                                            <input type="text" id="custom_odp" name="odp_id"
                                                value="{{ $customer->odp_id }}"
                                                placeholder="Isi ODP jika tidak tersedia pada pilihan"
                                                class="form-control @error('odp_id') is-invalid @enderror">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="validationCustom01" class="form-label required">No HP</label>
                                        <input type="text" inputmode="numeric" name="phone"
                                            value="{{ $customer->phone }}"
                                            class="form-control @error('phone') is-invalid @enderror">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <input type="hidden" name="purpose" id="purpose"
                                        value="{{ $customer->transaction->purpose }}">
                                    @error('purpose')
                                        <span class="error">{{ $message }}</span>
                                    @enderror

                                    <div class="col-6 mb-2">
                                        <button class="btn btn-primary" type="button" id="get-location-btn">Get Lokasi
                                            Client</button>
                                    </div>

                                    <div class="d-flex justify-between gap-3">
                                        <div class="mb-3">
                                            <label class="form-label" for="latitude">Latitude</label>
                                            <input type="text" id="latitude" value="{{ $customer->latitude }}"
                                                name="latitude" class="form-control">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="longitude">Longitude</label>
                                            <input type="text" id="longitude" value="{{ $customer->longitude }}"
                                                name="longitude" class="form-control">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="validationCustom01" class="form-label required">Alamat</label>
                                        <textarea name="address" class="form-control" cols="30">{{ $customer->address }}</textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label w-100" for="technition">Pilih Teknisi</label>
                                        <select name="tecnition[]" class="form-control select2form" multiple>
                                            <option value="">Pilih Teknisi</option>
                                            @foreach ($technitian as $item)
                                                <option value="{{ $item->id }}"
                                                    @if (in_array($item->id, $customer->transaction->Transactiontechnition->pluck('user_id')->toArray())) selected @endif>
                                                    {{ $item->name }}
                                                </option>
                                            @endforeach
                                        </select>
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
                                                    <!-- Dynamic rows will be added here -->
                                                    @foreach ($customer->transaction->Transactionproduct as $index => $item)
                                                        <tr>
                                                            <th scope="row">{{ $index + 1 }}</th>
                                                            <td>
                                                                <select name="item_id[]" class="form-control select2form">
                                                                    <option selected>Pilih Barang</option>
                                                                    @foreach ($product as $unit)
                                                                        <option value="{{ $unit->id }}"
                                                                            {{ $unit->id == $item->product_id ? 'selected' : '' }}
                                                                            data-name="{{ $unit->name }}">
                                                                            {{ $unit->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td class="sn-modem-container" style="visibility: hidden;">
                                                                @php
                                                                    $snModemArray = json_decode(
                                                                        $item->transaksi->customer->sn_modem,
                                                                    );
                                                                    $snModemArray = array_filter(
                                                                        $snModemArray,
                                                                        function ($value) {
                                                                            return !empty($value);
                                                                        },
                                                                    );
                                                                    $snModemValue =
                                                                        count($snModemArray) > 0
                                                                            ? implode(', ', $snModemArray)
                                                                            : '';
                                                                @endphp
                                                                <input type="text" name="sn_modem[]"
                                                                    class="form-control" value="{{ $snModemValue }}">
                                                            </td>
                                                            <td>
                                                                <input type="text" name="quantity[]"
                                                                    class="form-control" inputmode="numeric"
                                                                    value="{{ $item->quantity }}">
                                                            </td>
                                                            <td>
                                                                <button type="button"
                                                                    class="btn btn-danger btn-sm delete-btn">Delete</button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>



                                    <div>
                                        <button class="btn btn-primary" type="submit">Submit</button>
                                    </div>
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
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-select2.init.js') }}"></script>

    <script>
        $(document).ready(function() {
            var initialZoneId = $('#zone_id').val();
            updateOdpOptions(initialZoneId);

            // Trigger ODP options update on zone change
            $('#zone_id').on('change', function() {
                var zone_id = $(this).val();
                updateOdpOptions(zone_id);
            });

            // Handle custom ODP input field
            $('#custom_odp').on('input', function() {
                var customOdp = $(this).val();
                if (customOdp !== '') {
                    $('#odp_id').val('').prop('disabled', true);
                } else {
                    $('#odp_id').prop('disabled', false);
                }
            });

            // Update ODP options based on the selected zone ID
            function updateOdpOptions(zone_id) {
                if (zone_id) {
                    $.ajax({
                        url: "{{ route('customer.getdataodp', ':zone_id') }}".replace(':zone_id', zone_id),
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#odp_id').empty().append('<option value="">Pilih Odp</option>');

                            if (data.length > 0) {
                                $.each(data, function(key, value) {
                                    $('#odp_id').append('<option value="' + value.name + '">' +
                                        value.name + '</option>');
                                });

                                // Check if the selected ODP value is already set (editing mode)
                                var selectedOdp = '{{ $customer->odp_id }}';
                                if (selectedOdp) {
                                    // If the selected ODP exists in the dropdown, select it
                                    if ($('#odp_id option[value="' + selectedOdp + '"]').length) {
                                        $('#odp_id').val(selectedOdp);
                                    } else {
                                        // If the selected ODP does not exist in the dropdown, show custom ODP input
                                        $('#custom_odp').val(selectedOdp);
                                        $('#odp_id').val('').prop('disabled', true);
                                        $('#custom-odp-container').show();
                                    }
                                }

                                $('#odp_id').prop('disabled', false);
                                $('#custom-odp-container').hide();
                            } else {
                                $('#odp_id').prop('disabled', true);
                                $('#custom-odp-container').show();
                            }
                        },
                        error: function() {
                            $('#odp_id').empty().append('<option value="">Pilih Odp</option>');
                            $('#odp_id').prop('disabled', true);
                            $('#custom-odp-container').show();
                        }
                    });
                } else {
                    $('#odp_id').empty().append('<option value="">Pilih Odp</option>');
                    $('#odp_id').prop('disabled', true);
                    $('#custom-odp-container').hide();
                }
            }

            $('form').on('submit', function(e) {
                var odp_id = $('#odp_id').val();
                var custom_odp = $('#custom_odp').val();

                if (odp_id) {
                    $('input[name="odp_id"]').val(odp_id);
                } else if (custom_odp) {
                    $('input[name="odp_id"]').val(custom_odp);
                } else {
                    e.preventDefault();
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

                // Add event listener for item selection change
                tableBody.find('select[name="item_id[]"]').last().on('change', function() {
                    toggleSnModemInput($(this)); // Show or hide SN Modem input
                    updateItemDropdown(); // Update dropdown options
                });

                // Initial toggle for SN Modem visibility based on the current selection
                toggleSnModemInput(tableBody.find('select[name="item_id[]"]').last());

                updateRowNumbers(); // Update row numbers
            });

            // Fungsi untuk menampilkan atau menyembunyikan input SN Modem
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

            // Update dropdown item yang digunakan
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

            // Cek visibilitas SN Modem pada data yang ada saat halaman dimuat
            $('#myTable tbody tr').each(function() {
                const selectElement = $(this).find('select[name="item_id[]"]');
                toggleSnModemInput(
                    selectElement); // Update visibilitas berdasarkan pilihan barang saat halaman dimuat
            });

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
                    e.preventDefault(); // Prevent form submission if duplicates found
                    alert('Some products are selected more than once. Please select different products.');
                }
            });


        });
    </script>
@endpush
