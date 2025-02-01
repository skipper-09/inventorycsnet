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
                        <li class="breadcrumb-item"><a href="{{ route('report.transaction-product') }}">Laporan Transaksi</a></li>
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
                               id="validationCustom01" value="{{ old('name', $formbuilder->name) }}">
                        @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label w-100" for="role_id">Pilih Role</label>
                        <select name="role_id" id="role_id" class="form-control select2form @error('role_id') is-invalid @enderror">
                            <option value="">Pilih Role</option>
                            @foreach ($role as $item)
                                <option value="{{ $item->id }}" 
                                    {{ old('role_id', $formbuilder->role_id) == $item->id ? 'selected' : '' }}>
                                    {{ $item->name }}
                                </option>
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
</div>
@endsection

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
<script src="{{ asset('assets/form-builder/form-builder.min.js') }}"></script>
<script src="{{ asset('assets/form-builder/form-render.min.js') }}"></script>

<script>
    jQuery(function($) {
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
                    field: '<select name="' + fieldData.name + '" class="form-control select2" id="' + fieldData.name + '"></select>',
                    onRender: function() {
                        var products = @json($product);
                        let selectElement = $('#' + fieldData.name);

                        products.forEach(product => {
                            let isSelected = (fieldData.value == product.id) ? 'selected' : '';
                            selectElement.append(new Option(product.name, product.id, isSelected));
                        });
                    }
                };
            }
        };

        var options = {
      controlOrder: [
        'text',
        'textarea'
      ]
    };
// var defaultFields = @json($contentform);
        // Pre-fill the form with the existing form data (e.g., $formbuilder->form_data)
        $('#fb-editor').formBuilder({
            fields,
            templates,
            formData:@json($contentform),
            onSave: function(evt, formdata) {
                var data = {
                    id: '{{ $formbuilder->id }}',
                    name: $('input[name="name"]').val(),
                    role_id: $('#role_id').val(),
                    form: formdata,
                    _token: '{{ csrf_token() }}'
                };
                saveForm(data);
            }
        });

        // Save the form data to the server (update existing data)
        function saveForm(data) {
            $.ajax({
                url: '{{ route('formbuilder.update', $formbuilder->id) }}', // Change to the update route
                method: 'PUT', // Use PUT method to update
                data: data,
                success: function(response) {
                    if (response.success) {
                        alert('Data updated successfully!');
                        window.location.href = '{{ route('formbuilder') }}'; // Redirect to the index or another page
                    } else {
                        alert('Error updating data');
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error: ' + error);
                    alert('Something went wrong');
                }
            });
        }
    });
</script>
@endpush
