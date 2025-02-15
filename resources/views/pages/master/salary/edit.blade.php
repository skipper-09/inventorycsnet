@extends('layouts.base')

@section('title', $title)

@push('css')
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    <style>
        .remove-row {
            cursor: pointer;
            color: #dc3545;
        }

        .remove-row:hover {
            color: #bd2130;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Edit {{ $title }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('salary') }}">{{ $title }}</a></li>
                            <li class="breadcrumb-item active">Edit {{ $title }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('salary.update', $salary->id) }}" method="POST" id="salaryForm">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <!-- Employee Information -->
                                <div class="col-md-6">
                                    <h5 class="mb-4">Informasi Karyawan</h5>
                                    <div class="mb-3">
                                        <label class="form-label required">Karyawan</label>
                                        <select name="employee_id" id="employee_id"
                                            class="form-control select2 @error('employee_id') is-invalid @enderror"
                                            required>
                                            <option value="">Pilih Karyawan</option>
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->id }}"
                                                    {{ old('employee_id', $salary->employee_id) == $employee->id ? 'selected' : '' }}>
                                                    {{ $employee->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('employee_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label required">Bulan Gaji</label>
                                        <select name="salary_month" id="salary_month"
                                            class="form-control select2 @error('salary_month') is-invalid @enderror"
                                            required>
                                            @foreach ($months as $month)
                                                <option value="{{ $month['value'] }}"
                                                    {{ old('salary_month', Carbon\Carbon::parse($salary->salary_month)->format('Y-m')) == $month['value'] ? 'selected' : '' }}>
                                                    {{ $month['label'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('salary_month')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Salary Information -->
                                <div class="col-md-6">
                                    <h5 class="mb-4">Informasi Gaji</h5>
                                    <div class="mb-3">
                                        <label class="form-label required">Gaji Pokok</label>
                                        <input type="number" name="basic_salary_amount" id="basic_salary_amount"
                                            class="form-control currency-input @error('basic_salary_amount') is-invalid @enderror"
                                            value="{{ old('basic_salary_amount', number_format($salary->basic_salary_amount, 0, ',', '')) }}"
                                            required>
                                        @error('basic_salary_amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label required">Bonus</label>
                                        <input type="number" name="bonus" id="bonus"
                                            class="form-control currency-input @error('bonus') is-invalid @enderror"
                                            value="{{ old('bonus', number_format($salary->bonus, 0, ',', '')) }}" required>
                                        @error('bonus')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Allowances Section -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5 class="mb-4">Tunjangan
                                        <button type="button" class="btn btn-sm btn-primary float-end"
                                            id="addAllowanceRow">
                                            <i class="fas fa-plus"></i> Tambah Tunjangan
                                        </button>
                                    </h5>
                                    <div id="allowancesContainer">
                                        @foreach ($salary->employee->allowances as $index => $allowance)
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <select name="allowances[{{ $index }}][allowance_type_id]"
                                                        class="form-select select2-allowance" required>
                                                        <option value="">Pilih Tipe Tunjangan</option>
                                                        @foreach ($allowance_types as $type)
                                                            <option value="{{ $type->id }}"
                                                                {{ $allowance->allowance_type_id == $type->id ? 'selected' : '' }}>
                                                                {{ $type->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-5">
                                                    <input type="number" name="allowances[{{ $index }}][amount]"
                                                        class="form-control currency-input allowance-amount"
                                                        value="{{ number_format($allowance->amount, 0, ',', '') }}" required>
                                                </div>
                                                <div class="col-md-1 d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-times remove-row"></i>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Deductions Section -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5 class="mb-4">Potongan
                                        <button type="button" class="btn btn-sm btn-primary float-end"
                                            id="addDeductionRow">
                                            <i class="fas fa-plus"></i> Tambah Potongan
                                        </button>
                                    </h5>
                                    <div id="deductionsContainer">
                                        @foreach ($salary->employee->deductions as $index => $deduction)
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <select name="deductions[{{ $index }}][deduction_type_id]"
                                                        class="form-select select2-deduction" required>
                                                        <option value="">Pilih Tipe Potongan</option>
                                                        @foreach ($deduction_types as $type)
                                                            <option value="{{ $type->id }}"
                                                                {{ $deduction->deduction_type_id == $type->id ? 'selected' : '' }}>
                                                                {{ $type->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-5">
                                                    <input type="number" name="deductions[{{ $index }}][amount]"
                                                        class="form-control currency-input deduction-amount"
                                                        value="{{ number_format($deduction->amount, 0, ',', '') }}" required>
                                                </div>
                                                <div class="col-md-1 d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-times remove-row"></i>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Salary Summary -->
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <h5 class="mb-4">Total Potongan dan Tunjangan</h5>
                                    <div class="mb-3">
                                        <label class="form-label">Total Tunjangan</label>
                                        <input type="text" id="total_allowance" class="form-control currency-input"
                                            readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Total Potongan</label>
                                        <input type="text" id="total_deduction" class="form-control currency-input"
                                            readonly>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h5 class="mb-4">Total Gaji</h5>
                                    <div class="mb-3">
                                        <label class="form-label">Total Gaji Bersih</label>
                                        <input type="text" id="total_salary"
                                            class="form-control currency-input fw-bold" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-save me-1"></i> Simpan Perubahan
                                </button>
                                <a href="{{ route('salary') }}" class="btn btn-secondary ms-2">
                                    <i class="fas fa-arrow-left me-1"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-select2.init.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2, .select2-allowance, .select2-deduction').select2({
                width: '100%'
            });

            // Add Allowance Row
            $('#addAllowanceRow').click(function() {
                const index = $('#allowancesContainer .row').length;
                addAllowanceRow(index);
            });

            // Add Deduction Row
            $('#addDeductionRow').click(function() {
                const index = $('#deductionsContainer .row').length;
                addDeductionRow(index);
            });

            // Remove row handler
            $(document).on('click', '.remove-row', function() {
                $(this).closest('.row').remove();
                calculateTotal();
            });

            // Amount change handler for all numeric inputs
            $(document).on('input', '.currency-input', function() {
                calculateTotal();
            });

            // Form submission handler
            $('#salaryForm').on('submit', function(e) {
                // Ensure all numeric inputs are stored correctly
                $('.currency-input').each(function() {
                    const numericValue = parseFloat($(this).val()) || 0;
                    $(this).val(numericValue);
                });
            });

            // Initial calculation
            calculateTotal();
        });

        function addAllowanceRow(index) {
            const row =
                `<div class="row mb-3">
                    <div class="col-md-6">
                        <select name="allowances[${index}][allowance_type_id]" class="form-select select2-allowance" required>
                            <option value="">Pilih Tipe Tunjangan</option>
                            @foreach ($allowance_types as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <input type="number" name="allowances[${index}][amount]" class="form-control currency-input allowance-amount" value="0" required>
                    </div>
                    <div class="col-md-1 d-flex align-items-center justify-content-center">
                        <i class="fas fa-times remove-row"></i>
                    </div>
                </div>`;

            $('#allowancesContainer').append(row);
            initializeNewRow($('#allowancesContainer .row:last'));
        }

        function addDeductionRow(index) {
            const row =
                `<div class="row mb-3">
                    <div class="col-md-6">
                        <select name="deductions[${index}][deduction_type_id]" class="form-select select2-deduction" required>
                            <option value="">Pilih Tipe Potongan</option>
                            @foreach ($deduction_types as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <input type="number" name="deductions[${index}][amount]" class="form-control currency-input deduction-amount" value="0" required>
                    </div>
                    <div class="col-md-1 d-flex align-items-center justify-content-center">
                        <i class="fas fa-times remove-row"></i>
                    </div>
                </div>`;

            $('#deductionsContainer').append(row);
            initializeNewRow($('#deductionsContainer .row:last'));
        }

        function initializeNewRow(row) {
            // Initialize Select2
            row.find('.select2-allowance, .select2-deduction').select2({
                width: '100%'
            });

            // No need for currency mask, just numeric input
        }

        function calculateTotal() {
            const basicSalary = parseFloat($('#basic_salary_amount').val()) || 0;
            const bonus = parseFloat($('#bonus').val()) || 0;

            // Calculate total allowances
            let totalAllowances = 0;
            $('.allowance-amount').each(function() {
                totalAllowances += parseFloat($(this).val()) || 0;
            });

            // Calculate total deductions
            let totalDeductions = 0;
            $('.deduction-amount').each(function() {
                totalDeductions += parseFloat($(this).val()) || 0;
            });

            // Update summary fields with raw numeric values (no decimal formatting)
            $('#total_allowance').val(totalAllowances);
            $('#total_deduction').val(totalDeductions);

            // Calculate and update total salary
            const totalSalary = basicSalary + bonus + totalAllowances - totalDeductions;
            $('#total_salary').val(totalSalary);
        }
    </script>
@endpush
