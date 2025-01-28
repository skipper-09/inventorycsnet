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
                            <li class="breadcrumb-item"><a href="{{ route('workproduct') }}">{{ $title }}</a></li>
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
                            <form action="{{ route('workproduct.update', ['id' => $transaction->id]) }}" method="POST"
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
                                                    {{ $item->id == $transaction->branch_id ? 'selected' : '' }}>
                                                    {{ $item->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('branch_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="name" class="form-label required">Nama Pekerjaan</label>
                                        <input type="text" name="name"
                                            value="{{ $transaction->WorkTransaction->name }}"
                                            class="form-control @error('name') is-invalid @enderror" id="name">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label w-100" for="purpose">Pilih Tujuan</label>
                                        <select name="purpose" id="purpose"
                                            class="form-control select2form @error('purpose') is-invalid @enderror">
                                            <option value="">Pilih Tujuan</option>
                                            <option value="psb" {{ $transaction->purpose == 'psb' ? 'selected' : '' }}>
                                                Pemasangan Baru
                                            </option>
                                            <option value="repair"
                                                {{ $transaction->purpose == 'repair' ? 'selected' : '' }}>
                                                Perbaikan
                                            </option>
                                        </select>
                                        @error('purpose')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label w-100" for="technitian">Pilih Teknisi</label>
                                        <select name="technitian[]"
                                            class="form-control select2form @error('technitian') is-invalid @enderror"
                                            multiple>
                                            @foreach ($technitian as $item)
                                                <option value="{{ $item->id }}"
                                                    {{ in_array($item->id, $transaction->Transactiontechnition->pluck('user_id')->toArray()) ? 'selected' : '' }}>
                                                    {{ $item->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('technitian')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <hr class="text-bg-info">

                                    <div class="col-md-6">
                                        <button type="button" class="btn btn-primary btn-sm mb-3" id="addRow">
                                            Tambah Barang
                                        </button>
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
                                                    @foreach ($transaction->Transactionproduct as $index => $item)
                                                        <tr>
                                                            <th scope="row">{{ $index + 1 }}</th>
                                                            <td>
                                                                <select name="item_id[]" class="form-control select2form">
                                                                    <option value="">Pilih Barang</option>
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
                                                                    $snModem = $item->sn_modem ?? null;
                                                                    if ($snModem) {
                                                                        $snModemArray = json_decode($snModem, true);
                                                                        if (is_array($snModemArray)) {
                                                                            $snModemArray = array_filter($snModemArray);
                                                                            $snModemValue = !empty($snModemArray)
                                                                                ? implode(', ', $snModemArray)
                                                                                : '';
                                                                        } else {
                                                                            $snModemValue = $snModem;
                                                                        }
                                                                    } else {
                                                                        $snModemValue = '';
                                                                    }
                                                                @endphp
                                                                <input type="text" name="sn_modem[]" class="form-control"
                                                                    value="{{ $snModemValue }}">
                                                            </td>
                                                            <td>
                                                                <input type="number" name="quantity[]" class="form-control"
                                                                    value="{{ $item->quantity }}" min="1">
                                                            </td>
                                                            <td>
                                                                <button type="button"
                                                                    class="btn btn-danger btn-sm delete-btn">
                                                                    Delete
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="mt-3">
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
