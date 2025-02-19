<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji - {{ $employee->name }} - {{ $salaryMonth }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }

        .header img {
            width: 120px;
            margin-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header p {
            margin: 5px 0;
            font-size: 16px;
        }

        .employee-info {
            margin-bottom: 30px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            font-size: 14px;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .amount-column {
            text-align: right;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0 10px 0;
            color: #2c3e50;
        }

        .total-row {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .grand-total {
            background-color: #2c3e50;
            color: white;
        }

        .footer {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }

        .signature-section {
            text-align: center;
            width: 200px;
        }

        .signature-section .title {
            font-weight: bold;
            margin-bottom: 50px;
        }

        .signature-section .name {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .signature-section .position {
            font-size: 14px;
            color: #666;
        }

        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="{{ public_path('path_to_logo.png') }}" alt="Company Logo">
            <h1>Slip Gaji Karyawan</h1>
            <p>Periode: {{ $salaryMonth }}</p>
        </div>

        <table class="table employee-info">
            <tr>
                <th width="30%">Nama Karyawan</th>
                <td>{{ $employee->name }}</td>
                <th width="20%">NIK</th>
                <td>{{ $employee->nik }}</td>
            </tr>
            <tr>
                <th>Jabatan</th>
                <td>{{ $employee->position->name ?? '-' }}</td>
                <th>Departemen</th>
                <td>{{ $employee->department->name ?? '-' }}</td>
            </tr>
        </table>

        <div class="section-title">Penghasilan</div>
        <table class="table">
            <tr>
                <th width="60%">Komponen</th>
                <th class="amount-column">Jumlah (Rp)</th>
            </tr>
            <tr>
                <td>Gaji Pokok</td>
                <td class="amount-column">{{ number_format($salary->basic_salary_amount, 0, ',', '.') }}</td>
            </tr>
            @if ($salary->bonus > 0)
                <tr>
                    <td>Bonus</td>
                    <td class="amount-column">{{ number_format($salary->bonus, 0, ',', '.') }}</td>
                </tr>
            @endif
        </table>

        <div class="section-title">Tunjangan</div>
        <table class="table">
            <tr>
                <th width="60%">Jenis Tunjangan</th>
                <th class="amount-column">Jumlah (Rp)</th>
            </tr>
            @forelse ($allowances as $allowance)
                <tr>
                    <td>{{ $allowance->allowanceType->name }}</td>
                    <td class="amount-column">{{ number_format($allowance->amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="text-center">Tidak ada tunjangan</td>
                </tr>
            @endforelse
            <tr class="total-row">
                <td>Total Tunjangan</td>
                <td class="amount-column">{{ number_format($total_allowances, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="section-title">Potongan</div>
        <table class="table">
            <tr>
                <th width="60%">Jenis Potongan</th>
                <th class="amount-column">Jumlah (Rp)</th>
            </tr>
            @forelse ($deductions as $deduction)
                <tr>
                    <td>{{ $deduction->deductionType->name }}</td>
                    <td class="amount-column">{{ number_format($deduction->amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="text-center">Tidak ada potongan</td>
                </tr>
            @endforelse
            <tr class="total-row">
                <td>Total Potongan</td>
                <td class="amount-column">{{ number_format($total_deductions, 0, ',', '.') }}</td>
            </tr>
        </table>

        <table class="table">
            <tr class="grand-total">
                <td width="60%"><strong>Take Home Pay</strong></td>
                <td class="amount-column">
                    <strong>{{ number_format($net_salary, 0, ',', '.') }}</strong>
                </td>
            </tr>
        </table>

        <div class="footer">
            <div class="signature-section">
                <div class="title">Dibuat oleh,</div>
                <div class="name">Ika Agustina</div>
                <div class="position">HRD Manager</div>
            </div>

            <div class="signature-section">
                <div class="title">Diterima oleh,</div>
                <div class="name">{{ $employee->name }}</div>
                <div class="position">{{ $employee->position->name ?? 'Karyawan' }}</div>
            </div>

            <div class="signature-section">
                <div class="title">Mengetahui,</div>
                <div class="name">Yuwono Cahyadi</div>
                <div class="position">Direktur</div>
            </div>
        </div>
    </div>
</body>

</html>
