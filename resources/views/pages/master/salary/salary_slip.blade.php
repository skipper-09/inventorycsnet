<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Slip Gaji - {{ $employee->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            font-size: 11px;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }

        .company-logo {
            margin-bottom: 5px;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .company-address {
            margin-bottom: 2px;
        }

        .document-title {
            font-size: 16px;
            font-weight: bold;
            margin: 10px 0;
            text-align: center;
        }

        .employee-info {
            margin-bottom: 15px;
        }

        .employee-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .employee-info td {
            padding: 3px;
        }

        .row {
            display: flex;
            margin-bottom: 10px;
        }

        .col {
            flex: 1;
            padding: 0 5px;
        }

        h3 {
            margin: 5px 0;
            font-size: 13px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .table th,
        .table td {
            padding: 4px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .summary {
            margin-top: 15px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .summary-table {
            width: 50%;
            margin-left: auto;
        }

        .summary-table td {
            padding: 3px;
        }

        .net-salary {
            font-weight: bold;
            font-size: 14px;
            color: #2a6496;
        }

        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 9px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }

        .text-right {
            text-align: right;
        }

        .signature-table {
            width: 100%;
            margin-top: 14.5rem;
        }

        .signature-table td {
            width: 30%;
            padding: 2px;
            text-align: center;
        }

        .signature-space {
            height: 30px;
        }

        .signature-line {
            padding-top: 2px;
        }
        
        .subtotal {
            font-weight: bold;
            border-top: 1px solid #aaa;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-logo">
                @if (function_exists('Setting') && Setting('logo'))
                    <img src="{{ asset('/storage/' . Setting('logo')) }}" alt="Company Logo">
                @else
                    <div style="height: 50px;"></div>
                @endif
            </div>
            <div class="company-name">
                {{ function_exists('Setting') ? Setting('name') : 'Company Name' }}
            </div>
            <div class="company-address">
                {{ function_exists('Setting') ? Setting('address') : 'Company Address' }}
            </div>
        </div>

        <div class="document-title">SLIP GAJI KARYAWAN</div>

        <!-- Employee Information -->
        <div class="employee-info">
            <table>
                <tr>
                    <td width="150">Nama Karyawan</td>
                    <td width="10">:</td>
                    <td><strong>{{ $employee->name }}</strong></td>
                    <td width="150">Periode Gaji</td>
                    <td width="10">:</td>
                    <td><strong>{{ $salaryMonth }}</strong></td>
                </tr>
                <tr>
                    <td>Departemen</td>
                    <td>:</td>
                    <td>{{ $employee->department ? $employee->department->name : '-' }}</td>
                    <td>Tanggal Cetak</td>
                    <td>:</td>
                    <td>{{ date('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td>Posisi</td>
                    <td>:</td>
                    <td>{{ $employee->position ? $employee->position->name : '-' }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
        </div>

        <div class="row">
            <!-- Earnings -->
            <div class="col">
                <h3>Penghasilan</h3>
                <table class="table">
                    <tr>
                        <th>Deskripsi</th>
                        <th class="text-right">Jumlah</th>
                    </tr>
                    <tr>
                        <td>Gaji Pokok</td>
                        <td class="text-right">Rp {{ number_format($salary->basic_salary_amount, 0, ',', '.') }}</td>
                    </tr>
                    @if ($salary->bonus > 0)
                        <tr>
                            <td>Bonus</td>
                            <td class="text-right">Rp {{ number_format($salary->bonus, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                </table>
            </div>

            <!-- Allowances -->
            <div class="col">
                <h3>Tunjangan</h3>
                <table class="table">
                    <tr>
                        <th>Deskripsi</th>
                        <th class="text-right">Jumlah</th>
                    </tr>
                    @forelse ($allowances as $allowance)
                        <tr>
                            <td>{{ $allowance['type'] }}</td>
                            <td class="text-right">Rp {{ number_format($allowance['amount'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" style="text-align: center;">Tidak ada tunjangan</td>
                        </tr>
                    @endforelse
                    
                    <!-- Only show subtotal if there are allowances -->
                    @if(count($allowances) > 0)
                        <tr class="subtotal">
                            <td>Total Tunjangan</td>
                            <td class="text-right">Rp {{ number_format($total_allowances, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                </table>
            </div>

            <!-- Deductions -->
            <div class="col">
                <h3>Potongan</h3>
                <table class="table">
                    <tr>
                        <th>Deskripsi</th>
                        <th class="text-right">Jumlah</th>
                    </tr>
                    @forelse ($deductions as $deduction)
                        <tr>
                            <td>{{ $deduction['type'] }}</td>
                            <td class="text-right">Rp {{ number_format($deduction['amount'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" style="text-align: center;">Tidak ada potongan</td>
                        </tr>
                    @endforelse
                    
                    <!-- Only show subtotal if there are deductions -->
                    @if(count($deductions) > 0)
                        <tr class="subtotal">
                            <td>Total Potongan</td>
                            <td class="text-right">Rp {{ number_format($total_deductions, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>

        <!-- Summary -->
        <div class="summary">
            <table class="summary-table">
                <tr>
                    <td width="200">Total Penghasilan</td>
                    <td width="20">:</td>
                    <td class="text-right">Rp
                        {{ number_format($salary->basic_salary_amount + $total_allowances + $salary->bonus, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td>Total Potongan</td>
                    <td>:</td>
                    <td class="text-right">Rp {{ number_format($total_deductions, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Gaji Bersih</td>
                    <td>:</td>
                    <td class="text-right net-salary">Rp {{ number_format($net_salary, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <!-- Signature -->
        <table class="signature-table">
            <tr>
                <td width="50%">Dibuat oleh,</td>
                <td width="50%">Disetujui oleh,</td>
            </tr>
            <tr>
                <td class="signature-space"></td>
                <td class="signature-space"></td>
            </tr>
            <tr>
                <td class="signature-line">HRD</td>
                <td class="signature-line">Owner</td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="footer">
            <p>Dokumen ini dihasilkan secara otomatis dan sah tanpa tanda tangan.</p>
        </div>
    </div>
</body>

</html>