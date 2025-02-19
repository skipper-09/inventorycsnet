<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji - {{ $salaryMonth }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .header p {
            margin: 0;
            font-size: 16px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table th,
        .table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .table th {
            background-color: #f2f2f2;
        }

        .total {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Slip Gaji</h1>
        <p>Bulan: {{ $salaryMonth }}</p>
    </div>

    <table class="table">
        <tr>
            <th>Nama Karyawan</th>
            <td>{{ $salary->employee->name }}</td>
        </tr>
        <tr>
            <th>Bulan Gaji</th>
            <td>{{ $salaryMonth }}</td>
        </tr>
    </table>

    <h3>Rincian Gaji</h3>
    <table class="table">
        <tr>
            <th>Komponen</th>
            <th>Jumlah</th>
        </tr>
        <tr>
            <td>Gaji Pokok</td>
            <td>{{ number_format($salary->basic_salary_amount, 2) }}</td>
        </tr>
        <tr>
            <td>Bonus</td>
            <td>{{ number_format($salary->bonus, 2) }}</td>
        </tr>
        @foreach ($allowances as $allowance)
            <tr>
                <td>Tunjangan {{ $allowance->allowanceType->name }}</td>
                <td>{{ number_format($allowance->amount, 2) }}</td>
            </tr>
        @endforeach
        @foreach ($deductions as $deduction)
            <tr>
                <td>Potongan {{ $deduction->deductionType->name }}</td>
                <td>{{ number_format($deduction->amount, 2) }}</td>
            </tr>
        @endforeach
        <tr class="total">
            <td>Total Gaji</td>
            <td>{{ number_format($salary->total_salary, 2) }}</td>
        </tr>
    </table>
</body>

</html>
