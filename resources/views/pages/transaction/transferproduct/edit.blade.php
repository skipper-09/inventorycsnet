@extends('layouts.base')
@section('title', $title)

@push('css')
    <!-- DataTables -->
    <link href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
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
                            <form action="{{ route('transfer.update', $transfer->id) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label required">Dari Cabang</label>
                                            <select name="from_branch" class="form-control select2 @error('from_branch') is-invalid @enderror">
                                                <option value="">Pilih Cabang</option>
                                                @foreach($branch as $b)
                                                    <option value="{{ $b->id }}" {{ $transfer->branch_id == $b->id ? 'selected' : '' }}>
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
                                            <select name="to_branch" class="form-control select2 @error('to_branch') is-invalid @enderror">
                                                <option value="">Pilih Cabang</option>
                                                @foreach($branch as $b)
                                                    <option value="{{ $b->id }}" {{ $transfer->to_branch == $b->id ? 'selected' : '' }}>
                                                        {{ $b->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('to_branch')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-12">
                                        <h5>Produk</h5>
                                        <div id="product-container">
                                            @foreach($transfer->Transactionproduct as $index => $tp)
                                                <div class="row product-row mb-3">
                                                    <div class="col-md-6">
                                                        <select name="products[{{ $index }}][id]" class="form-control select2 product-select @error('products.' . $index . '.id') is-invalid @enderror">
                                                            <option value="">Pilih Produk</option>
                                                            @foreach($product as $p)
                                                                <option value="{{ $p->id }}" {{ $tp->product_id == $p->id ? 'selected' : '' }}>
                                                                    {{ $p->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('products.' . $index . '.id')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="number" 
                                                            name="products[{{ $index }}][quantity]" 
                                                            value="{{ $tp->quantity }}"
                                                            class="form-control @error('products.' . $index . '.quantity') is-invalid @enderror" 
                                                            placeholder="Jumlah">
                                                        @error('products.' . $index . '.quantity')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-danger remove-product" {{ $index == 0 && count($transfer->Transactionproduct) == 1 ? 'style=display:none' : '' }}>
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

    <script>
        $(document).ready(function() {
            // Add product row
            $('#add-product').click(function() {
                const productCount = $('.product-row').length;
                const template = `
                    <div class="row product-row mb-3">
                        <div class="col-md-6">
                            <select name="products[${productCount}][id]" class="form-control select2 product-select @error('products.${productCount}.id') is-invalid @enderror">
                                <option value="">Pilih Produk</option>
                                @foreach($product as $p)
                                    <option value="{!! $p->id !!}">{!! $p->name !!}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="number" 
                                name="products[${productCount}][quantity]" 
                                class="form-control @error('products.${productCount}.quantity') is-invalid @enderror" 
                                placeholder="Jumlah">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger remove-product">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
                
                $('#product-container').append(template);
                
                // Reinitialize Select2 for new row
                $('#product-container .product-row:last-child .select2').select2();
                
                updateRemoveButtons();
            });

            // Remove product row
            $(document).on('click', '.remove-product', function() {
                $(this).closest('.product-row').remove();
                updateRemoveButtons();
                reindexProducts();
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

            // Reindex product inputs after removal
            function reindexProducts() {
                $('.product-row').each(function(index) {
                    const row = $(this);
                    row.find('select[name^="products["]').attr('name', `products[${index}][id]`);
                    row.find('input[name^="products["]').attr('name', `products[${index}][quantity]`);
                });
            }

            // Initialize remove buttons on page load
            updateRemoveButtons();

            // Form validation
            $('form').submit(function(event) {
                const fromBranch = $('select[name="from_branch"]').val();
                const toBranch = $('select[name="to_branch"]').val();

                if (fromBranch === toBranch) {
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
            });
        });
    </script>
@endpush