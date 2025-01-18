@extends('layouts.base')
@section('title', $title)

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Edit Product {{ $role->name }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('productrole.update', ['id' => $role->id]) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h5 class="mb-0">Products</h5>
                                    <button type="button" id="select-all-btn" class="btn btn-sm btn-primary"
                                        onclick="toggleSelectAll()">
                                        Select All
                                    </button>
                                </div>

                                <div class="row mx-0">
                                    @foreach ($products as $product)
                                        <div class="col-lg-4 col-md-6 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input product-checkbox" type="checkbox"
                                                    name="product_id[]" id="product_{{ $product->id }}"
                                                    value="{{ $product->id }}"
                                                    {{ in_array($product->id, $selectedProducts) ? 'checked' : '' }}
                                                    onchange="checkIfAllSelected()">
                                                <label class="form-check-label" for="product_{{ $product->id }}">
                                                    {{ $product->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                @error('product_id')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary me-2">Save Changes</button>
                                <a href="{{ route('productrole') }}" class="btn btn-secondary">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        let allSelected = false;

        function toggleSelectAll() {
            const checkboxes = document.querySelectorAll('.product-checkbox');
            allSelected = !allSelected;

            checkboxes.forEach((checkbox) => {
                checkbox.checked = allSelected;
            });

            updateSelectAllButton();
        }

        function checkIfAllSelected() {
            const checkboxes = document.querySelectorAll('.product-checkbox');
            allSelected = Array.from(checkboxes).every(checkbox => checkbox.checked);
            updateSelectAllButton();
        }

        function updateSelectAllButton() {
            const selectAllBtn = document.getElementById('select-all-btn');
            selectAllBtn.textContent = allSelected ? 'Unselect All' : 'Select All';
        }

        document.addEventListener('DOMContentLoaded', function() {
            checkIfAllSelected();
        });
    </script>
@endpush
