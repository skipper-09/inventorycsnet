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
            font-size: 11px; /* Reduced font size */
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            padding: 10px 0; /* Reduced padding */
            border-bottom: 1px solid #ddd; /* Thinner border */
        }

        .company-name {
            font-size: 18px; /* Reduced size */
            font-weight: bold;
            margin-bottom: 2px; /* Reduced margin */
        }

        .company-address {
            margin-bottom: 2px; /* Reduced margin */
        }

        .document-title {
            font-size: 16px; /* Reduced size */
            font-weight: bold;
            margin: 10px 0; /* Reduced margin */
            text-align: center;
        }

        .employee-info {
            margin-bottom: 15px; /* Reduced margin */
        }

        .employee-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .employee-info td {
            padding: 3px; /* Reduced padding */
        }

        .row {
            display: flex;
            margin-bottom: 10px; /* Reduced margin */
        }

        .col {
            flex: 1;
            padding: 0 5px; /* Reduced padding */
        }

        h3 {
            margin: 5px 0; /* Reduced margin */
            font-size: 13px; /* Reduced size */
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px; /* Reduced margin */
        }

        .table th,
        .table td {
            padding: 4px; /* Reduced padding */
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .summary {
            margin-top: 15px; /* Reduced margin */
            border-top: 1px solid #ddd; /* Thinner border */
            padding-top: 10px; /* Reduced padding */
        }

        .summary-table {
            width: 50%;
            margin-left: auto;
        }

        .summary-table td {
            padding: 3px; /* Reduced padding */
        }

        .net-salary {
            font-weight: bold;
            font-size: 14px; /* Reduced size */
            color: #2a6496;
        }

        .footer {
            margin-top: 15px; /* Reduced margin */
            text-align: center;
            font-size: 9px; /* Reduced size */
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 5px; /* Reduced padding */
        }

        .text-right {
            text-align: right;
        }

        .signature-table {
            width: 100%;
            margin-top: 15px; /* Reduced margin */
        }

        .signature-table td {
            width: 30%;
            padding: 2px;
            text-align: center;
        }

        .signature-space {
            height: 30px; /* Reduced height */
        }

        .signature-line {
            padding-top: 2px; /* Reduced padding */
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-name">{{ $company['name'] }}</div>
            <div class="company-address">{{ $company['address'] }}</div>
            <div>{{ $company['phone'] }} | {{ $company['email'] }}</div>
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
                    <td>{{ $employee->department->name }}</td>
                    <td>Tanggal Cetak</td>
                    <td>:</td>
                    <td>{{ date('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td>Posisi</td>
                    <td>:</td>
                    <td>{{ $employee->position->name }}</td>
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
                    @foreach ($allowances as $allowance)
                        <tr>
                            <td>Tunjangan {{ $allowance['type'] }}</td>
                            <td class="text-right">Rp {{ number_format($allowance['amount'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
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
                    @foreach ($deductions as $deduction)
                        <tr>
                            <td>{{ $deduction['type'] }}</td>
                            <td class="text-right">Rp {{ number_format($deduction['amount'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    @if (count($deductions) == 0)
                        <tr>
                            <td colspan="2" style="text-align: center;">Tidak ada potongan</td>
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
        <table class="signature-table" style="margin-top: 21rem;">
            <tr>
                <td width="66%">Dibuat oleh,</td>
                <td width="66%">Disetujui oleh,</td>
                {{-- <td width="33%">Diterima oleh,</td> --}}
            </tr>
            <tr>
                <td class="signature-space"></td>
                <td class="signature-space"></td>
                {{-- <td class="signature-space"></td> --}}
            </tr>
            <tr>
                <td class="signature-line">HRD</td>
                <td class="signature-line">Owner</td>
                {{-- <td class="signature-line">{{ $employee->name }}</td> --}}
            </tr>
        </table>

        <!-- Footer -->
        <div class="footer">
            <p>Dokumen ini dihasilkan secara otomatis dan sah tanpa tanda tangan.</p>
        </div>
    </div>
</body>

</html>