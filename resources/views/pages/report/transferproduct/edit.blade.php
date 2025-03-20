@extends('layouts.base')
@section('title', $title)

@push('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />

    <style>
        .signature-container {
            margin-bottom: 20px;
        }

        .signature-pad-wrapper {
            position: relative;
            width: 100%;
            max-width: 600px;
        }

        .signature-pad {
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #fff;
            width: 100%;
            height: 200px;
            margin-bottom: 10px;
        }

        .signature-pad canvas {
            width: 100%;
            height: 100%;
        }

        .signature-buttons {
            margin-top: 10px;
            display: flex;
            gap: 10px;
        }

        .signature-label {
            font-weight: bold;
            margin-bottom: 8px;
            display: block;
        }

        .existing-signature {
            max-width: 100%;
            height: auto;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 10px;
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
                            <li class="breadcrumb-item"><a href="{{ route('transfer') }}">Pemindahan Barang</a></li>
                            <li class="breadcrumb-item active">{{ $title }}</li>
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
                            @if ($errors->any())
                                <div class="alert alert-label-danger alert-dismissible fade show mb-4" role="alert">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="ri-error-warning-fill fs-16 align-middle me-2"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <strong class="d-block mb-1">Please check the following errors:</strong>
                                            <div class="text-muted">
                                                @foreach ($errors->all() as $error)
                                                    <div class="mb-1">{{ $error }}</div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                </div>
                            @endif
                            <form action="{{ route('transfer.update', $transfer->id) }}" method="POST"
                                enctype="multipart/form-data" class="needs-validation" novalidate>
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label required">Dari Cabang</label>
                                            <select name="from_branch"
                                                class="form-control select2 @error('from_branch') is-invalid @enderror">
                                                <option value="">Pilih Cabang</option>
                                                @foreach ($branch as $b)
                                                    <option value="{{ $b->id }}"
                                                        {{ $transfer->branch_id == $b->id ? 'selected' : '' }}>
                                                        {{ $b->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('from_branch')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label required">Ke Cabang</label>
                                            <select name="to_branch"
                                                class="form-control select2 @error('to_branch') is-invalid @enderror">
                                                <option value="">Pilih Cabang</option>
                                                @foreach ($branch as $b)
                                                    <option value="{{ $b->id }}"
                                                        {{ $transfer->to_branch == $b->id ? 'selected' : '' }}>
                                                        {{ $b->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('to_branch')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label required">Penanggung Jawab</label>
                                            <select name="technitian_id"
                                                class="form-control select2 @error('technitian_id') is-invalid @enderror">
                                                <option value="">Pilih Penanggung Jawab</option>
                                                @foreach ($technitians as $t)
                                                    <option value="{{ $t->id }}"
                                                        {{ $transfer->assign && $transfer->assign->technitian_id == $t->id ? 'selected' : '' }}>
                                                        {{ $t->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('technitian_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-12">
                                        <h5>Produk</h5>
                                        <div id="product-container">
                                            @foreach ($transfer->Transactionproduct as $index => $tp)
                                                <div class="row product-row mb-3">
                                                    <div class="col-md-6">
                                                        <select name="products[{{ $index }}][id]"
                                                            class="form-control select2 product-select">
                                                            <option value="">Pilih Produk</option>
                                                            @foreach ($product as $p)
                                                                <option value="{{ $p->id }}"
                                                                    {{ $tp->product_id == $p->id ? 'selected' : '' }}>
                                                                    {{ $p->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="number"
                                                            name="products[{{ $index }}][quantity]"
                                                            class="form-control" placeholder="Jumlah"
                                                            value="{{ $tp->quantity }}">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-danger remove-product">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <button type="button" class="btn btn-success" id="add-product">
                                            <i class="fas fa-plus"></i> Tambah Produk
                                        </button>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="signature-container">
                                            <label class="signature-label">Tanda Tangan Pengirim</label>
                                            @if ($transfer->assign && $transfer->assign->owner_signature)
                                                <div class="existing-signature mb-2">
                                                    <img src="{{ Storage::url($transfer->assign->owner_signature) }}"
                                                        alt="Owner Signature" class="img-fluid">
                                                </div>
                                            @endif
                                            <div class="signature-pad-wrapper">
                                                <div class="signature-pad" id="ownerSignaturePad"></div>
                                                <input type="hidden" name="owner_signature" id="ownerSignatureInput">
                                                <div class="signature-buttons">
                                                    <button type="button" class="btn btn-secondary btn-sm"
                                                        onclick="clearSignature('ownerSignaturePad')">
                                                        <i class="bx bx-x"></i> Clear
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="signature-container">
                                            <label class="signature-label">Tanda Tangan Penanggung Jawab</label>
                                            @if ($transfer->assign && $transfer->assign->technitian_signature)
                                                <div class="existing-signature mb-2">
                                                    <img src="{{ Storage::url($transfer->assign->technitian_signature) }}"
                                                        alt="Technician Signature" class="img-fluid">
                                                </div>
                                            @endif
                                            <div class="signature-pad-wrapper">
                                                <div class="signature-pad" id="technitianSignaturePad"></div>
                                                <input type="hidden" name="technitian_signature"
                                                    id="technitianSignatureInput">
                                                <div class="signature-buttons">
                                                    <button type="button" class="btn btn-secondary btn-sm"
                                                        onclick="clearSignature('technitianSignaturePad')">
                                                        <i class="bx bx-x"></i> Clear
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <button class="btn btn-primary" type="submit">Update</button>
                                        <a href="{{ route('transfer') }}" class="btn btn-secondary">Kembali</a>
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
    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-advanced.init.js') }}"></script>

    <!-- Select2 -->
    <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-select2.init.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>

    <script>
        // Initialize signature pads
        let ownerSignaturePad;
        let technitianSignaturePad;

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize owner signature pad
            const ownerCanvas = document.createElement('canvas');
            document.getElementById('ownerSignaturePad').appendChild(ownerCanvas);
            ownerSignaturePad = new SignaturePad(ownerCanvas);

            // Initialize technitian signature pad
            const technitianCanvas = document.createElement('canvas');
            document.getElementById('technitianSignaturePad').appendChild(technitianCanvas);
            technitianSignaturePad = new SignaturePad(technitianCanvas);

            // Handle window resize
            window.addEventListener('resize', resizeCanvas);
            resizeCanvas();

            // Handle form submission
            const form = document.querySelector('form');
            form.addEventListener('submit', function(event) {
                // Update signature inputs before submit if they exist
                if (!ownerSignaturePad.isEmpty()) {
                    document.getElementById('ownerSignatureInput').value = ownerSignaturePad.toDataURL();
                }
                if (!technitianSignaturePad.isEmpty()) {
                    document.getElementById('technitianSignatureInput').value = technitianSignaturePad
                        .toDataURL();
                }
            });
        });

        // Resize canvas
        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);

            [ownerSignaturePad, technitianSignaturePad].forEach(pad => {
                const canvas = pad.canvas;
                const width = canvas.offsetWidth;
                const height = canvas.offsetHeight;

                canvas.width = width * ratio;
                canvas.height = height * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
            });
        }

        // Clear signature
        function clearSignature(padId) {
            const pad = padId === 'ownerSignaturePad' ? ownerSignaturePad : technitianSignaturePad;
            const input = document.getElementById(padId === 'ownerSignaturePad' ? 'ownerSignatureInput' :
                'technitianSignatureInput');

            pad.clear();
            input.value = '';
        }

        $(document).ready(function() {
            // Initialize Select2 for existing elements
            initializeSelect2();

            // Function to initialize Select2
            function initializeSelect2() {
                $('.select2').select2({
                    allowClear: false
                });
            }

            // Function to update product indices
            function updateProductIndices() {
                $('.product-row').each(function(index) {
                    // Update select name
                    $(this).find('select.product-select')
                        .attr('name', `products[${index}][id]`);

                    // Update quantity input name
                    $(this).find('input[type="number"]')
                        .attr('name', `products[${index}][quantity]`);
                });
            }

            // Add product row
            $('#add-product').click(function() {
                const productCount = $('.product-row').length;
                const newRow = `
                    <div class="row product-row mb-3">
                        <div class="col-md-6">
                            <select name="products[${productCount}][id]" class="form-control select2 product-select">
                                <option value="">Pilih Produk</option>
                                @foreach ($product as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="number" name="products[${productCount}][quantity]" class="form-control" placeholder="Jumlah">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger remove-product">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;

                // Append new row and initialize Select2
                const $newRow = $(newRow);
                $('#product-container').append($newRow);
                $newRow.find('.select2').select2({
                    allowClear: false
                });

                updateRemoveButtons();
            });

            // Remove product row
            $(document).on('click', '.remove-product', function(e) {
                e.preventDefault();
                const $row = $(this).closest('.product-row');

                // Destroy Select2 instance before removing the row
                $row.find('.select2').each(function() {
                    if ($(this).data('select2')) {
                        $(this).select2('destroy');
                    }
                });

                // Remove the row
                $row.remove();

                // Update indices and remove buttons
                updateProductIndices();
                updateRemoveButtons();
            });

            // Update remove buttons visibility
            function updateRemoveButtons() {
                const rows = $('.product-row');
                if (rows.length === 1) {
                    rows.find('.remove-product').hide();
                } else {
                    rows.find('.remove-product').show();
                }
            }

            // Form validation
            $('form').submit(function(event) {
                const fromBranch = $('select[name="from_branch"]').val();
                const toBranch = $('select[name="to_branch"]').val();

                // Validate branch selection
                if (fromBranch === toBranch && fromBranch !== '') {
                    alert('Cabang asal dan tujuan tidak boleh sama!');
                    event.preventDefault();
                    return false;
                }

                // Check for duplicate products
                const products = {};
                let hasDuplicate = false;
                $('.product-select').each(function() {
                    const productId = $(this).val();
                    if (productId && productId in products) {
                        hasDuplicate = true;
                        return false;
                    }
                    if (productId) {
                        products[productId] = true;
                    }
                });

                if (hasDuplicate) {
                    alert('Produk tidak boleh duplikat!');
                    event.preventDefault();
                    return false;
                }

                // Validate required fields
                let hasEmptyRequired = false;
                $('.required').each(function() {
                    const input = $(this).closest('.mb-3').find('select, input').first();
                    if (!input.val()) {
                        hasEmptyRequired = true;
                        return false;
                    }
                });

                if (hasEmptyRequired) {
                    alert('Mohon lengkapi semua field yang wajib diisi!');
                    event.preventDefault();
                    return false;
                }
            });

            // Initial setup
            updateRemoveButtons();
        });
    </script>
@endpush
