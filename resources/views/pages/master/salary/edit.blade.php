@extends('layouts.base')

@section('title', $title)

@push('css')
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <div class="container-fluid">
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
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
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
                                            class="form-control select2 @error('employee_id') is-invalid @enderror">
                                            <option value="">Pilih Karyawan</option>
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->id }}"
                                                    data-allowances="{{ $employee->allowances->sum('amount') }}"
                                                    data-deductions="{{ $employee->deductions->sum('amount') }}"
                                                    {{ $salary->employee_id == $employee->id ? 'selected' : '' }}>
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
                                        <input type="month" name="salary_month" id="salary_month"
                                            class="form-control @error('salary_month') is-invalid @enderror"
                                            value="{{ old('salary_month', date('Y-m', strtotime($salary->salary_month))) }}">
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
                                        <input type="text" name="basic_salary_amount" id="basic_salary_amount"
                                            class="form-control @error('basic_salary_amount') is-invalid @enderror"
                                            value="{{ old('basic_salary_amount', number_format($salary->basic_salary_amount, 0, ',', '')) }}" oninput="calculateTotal()">
                                        @error('basic_salary_amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label required">Bonus</label>
                                        <input type="text" name="bonus" id="bonus"
                                            class="form-control @error('bonus') is-invalid @enderror"
                                            value="{{ old('bonus', number_format($salary->bonus, 0, ',', '')) }}" oninput="calculateTotal()">
                                        @error('bonus')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <h5 class="mb-4">Potongan dan Tunjangan</h5>

                                    <div class="mb-3">
                                        <label class="form-label">Total Potongan</label>
                                        <input type="text" id="deduction" class="form-control"
                                            value="{{ old('deduction', number_format($salary->deduction, 0, ',', '')) }}" readonly>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Total Tunjangan</label>
                                        <input type="text" id="allowance" class="form-control"
                                            value="{{ old('allowance', number_format($salary->allowance, 0, ',', '')) }}" readonly>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h5 class="mb-4">Total Gaji</h5>

                                    <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <div>
                                            <h6 class="fw-bold mb-0 text-white">Perhitungan:</h6>
                                            <p class="mb-0">Gaji Pokok + Bonus + Tunjangan - Potongan</p>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Total Gaji Bersih</label>
                                        <input type="text" id="total_salary" class="form-control fw-bold"
                                            value="{{ old('total_salary', number_format($salary->total_salary, 0, ',', '')) }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-save me-1"></i> Simpan
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
    <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-select2.init.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                width: '100%',
                placeholder: 'Pilih Karyawan'
            });

            // Employee change handler
            $('#employee_id').on('change', function() {
                const selected = $(this).find('option:selected');
                const allowances = selected.data('allowances') || 0;
                const deductions = selected.data('deductions') || 0;

                $('#allowance').val(formatCurrency(allowances));
                $('#deduction').val(formatCurrency(deductions));
                calculateTotal();
            });

            // Form submission handler
            $('#salaryForm').on('submit', function(e) {
                e.preventDefault();

                // Remove currency formatting before submitting
                const basicSalary = parseCurrency($('#basic_salary_amount').val());
                const bonus = parseCurrency($('#bonus').val());

                $('#basic_salary_amount').val(basicSalary);
                $('#bonus').val(bonus);

                this.submit();
            });
        });

        function calculateTotal() {
            const basicSalary = parseCurrency($('#basic_salary_amount').val());
            const bonus = parseCurrency($('#bonus').val());
            const allowances = parseCurrency($('#allowance').val());
            const deductions = parseCurrency($('#deduction').val());

            const total = basicSalary + bonus + allowances - deductions;
            $('#total_salary').val(formatCurrency(total));
        }

        function formatCurrency(number) {
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function parseCurrency(value) {
            return parseFloat(value.replace(/\./g, '').replace(/,/g, '.')) || 0;
        }
    </script>
@endpush
