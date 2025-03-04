<?php

namespace App\Exports;

use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\Models\Salary;

class SalaryExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithColumnWidths
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Nama Karyawan',
            'Gaji Pokok',
            'Bonus',
            'Potongan',
            'Total Gaji',
        ];
    }

    public function map($salary): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            date('d-M-Y', strtotime($salary->salary_month)),
            $salary->employee->name,
            $salary->basic_salary_amount,
            $salary->bonus,
            $salary->deduction,
            $salary->total_salary,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 15,  // Tanggal
            'C' => 30,  // Nama Karyawan
            'D' => 20,  // Gaji Pokok
            'E' => 20,  // Bonus
            'F' => 20,  // Potongan
            'G' => 20,  // Total Gaji
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $headings = $this->headings();
        $lastColumnIndex = count($headings);
        $lastColumn = chr(64 + $lastColumnIndex);
        $lastRow = $sheet->getHighestRow();

        // Apply borders to all cells
        $sheet->getStyle("A1:{$lastColumn}{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Header styling: Bold, background color, and centered alignment
        $headerStyle = [
            'font' => [
                'bold' => true,
                'size' => 12, // Increased font size for better readability
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD'], // Blue shade for header
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];

        // Content styling: Ensuring text is wrapped and aligned properly
        $contentStyle = [
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
                'wrapText' => true,  // Wrap long text within cells
            ],
        ];

        // Apply header style to the first row
        $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray($headerStyle);

        // Apply content style from row 2 onwards
        $sheet->getStyle("A2:{$lastColumn}{$lastRow}")->applyFromArray($contentStyle);

        // Specific column alignments
        $sheet->getStyle("A2:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // No column
        $sheet->getStyle("B2:B{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Tanggal
        $sheet->getStyle("C2:C{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT); // Nama Karyawan
        $sheet->getStyle("D2:G{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT); // Gaji Pokok, Bonus, Potongan, Total Gaji

        // Apply currency formatting to the money columns
        $sheet->getStyle("D2:G{$lastRow}")->getNumberFormat()->setFormatCode('[$Rp] #,##0');

        // Adjust row height for better readability
        $sheet->getRowDimension(1)->setRowHeight(25); // Taller header row
        for ($i = 2; $i <= $lastRow; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(-1); // Auto height based on content
        }

        return [];
    }
}