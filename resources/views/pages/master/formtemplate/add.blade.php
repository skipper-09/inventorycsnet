@extends('layouts.base')


@section('title', $title)


@section('content')
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">{{ $title }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('report.transaction-product') }}">Laporan
                                    Transaksi</a></li>
                            <li class="breadcrumb-item active">{{ $title }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaction Details -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="validationCustom01" class="form-label required">Nama Template</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                id="validationCustom01">
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label w-100" for="role_id">Piih Role</label>
                            <select name="role_id" id="role_id"
                                class="form-control select2form @error('role_id') is-invalid @enderror">
                                <option value="">Pilih Role</option>
                                @foreach ($role as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div id="fb-editor"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Details -->
        {{-- <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Detail Produk</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px;">No</th>
                                    <th>Barang</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transaction->transactionproduct as $index => $product)
                                <tr>
                                    <td class="text-center" style="width: 50px">{{ $index + 1 }}</td>
                                    <td>{{ $product->product->name ?? 'N/A' }}</td>
                                    <td>{{ $product->quantity }} {{ $product->product->unit->name ?? '' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('report.transaction-product') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
    </div>
@endsection

@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
    <script src="{{ asset('assets/form-builder/form-builder.min.js') }}"></script>
    <script src="{{ asset('assets/form-builder/form-render.min.js') }}"></script>
    <script>
        jQuery(function($) {
            //     $(document.getElementById('fb-editor')).formBuilder(({
            //     onSave: function(evt,formdata){
            //         var data = {
            //                     name: $('input[name="name"]').val(),
            //                     role_id: $('#role_id').val(),
            //                     form: formdata,
            //                     _token: '{{ csrf_token() }}' 
            //                 };
            //                 saveForm(data);
            //     }
            //   }));
            var products = @json($product);

            let fields = [{
                label: 'Data Product',
                attrs: {
                    type: 'select'
                },
                values: products.map((item, index) => {
                    return {
                        label: item.name,
                        value: item.id
                    };
                }),
                icon: '🔽'
            }];

            let templates = {

                productselect: function(fieldData) {
                    return {
                        field: '<select name="' + fieldData.name + '" class="form-control select2" id="' +
                            fieldData.name + '"></select>',
                        onRender: function() {
                            var products = @json($product);

                            let selectElement = $('#' + fieldData.name);

                            // Loop through products and populate options
                            products.forEach(product => {
                                // Check if the value of the product matches the selected value in fieldData
                                let isSelected = (fieldData.value == product.id) ? 'selected' :
                                    '';
                                selectElement.append(new Option(product.name, product.id,
                                    isSelected));
                            });

                            // Initialize Select2 (if using it)
                            // selectElement.select2();
                        }
                    };
                }
            };


            $('#fb-editor').formBuilder({
                fields,
                templates,
                onSave: function(evt, formdata) {
                    var data = {
                        name: $('input[name="name"]').val(),
                        role_id: $('#role_id').val(),
                        form: formdata,
                        _token: '{{ csrf_token() }}'
                    };
                    saveForm(data);
                }
            });


            function saveForm(data) {
                $.ajax({
                    url: '{{ route('formbuilder.store') }}',
                    method: 'POST',
                    data: data,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Data saved successfully!',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.href = '{{ route('formbuilder') }}';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error saving data',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle any errors
                        console.log('Error: ' + error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Something went wrong',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            }
        });
    </script>
@endpush
