<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class WorkScheduleExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithColumnWidths, WithTitle, WithCustomStartCell
{
    protected $schedules;
    protected $employee;
    protected $startDate;
    protected $endDate;

    public function __construct($schedules, $employee, $startDate, $endDate)
    {
        $this->schedules = $schedules;
        $this->employee = $employee;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return $this->schedules;
    }

    public function title(): string
    {
        return 'Jadwal Kerja';
    }

    public function startCell(): string
    {
        return 'A5';
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Status',
            'Shift',
            'Jam Masuk',
            'Jam Keluar',
        ];
    }

    public function map($schedule): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            Carbon::parse($schedule->schedule_date)->format('d-M-Y'),
            $schedule->status == 'work' ? 'Kerja' : 'Libur',
            $schedule->status == 'work' ? optional($schedule->shift)->name : '-',
            $schedule->status == 'work' ? optional($schedule->shift)->shift_start : '-',
            $schedule->status == 'work' ? optional($schedule->shift)->shift_end : '-',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 15,  // Tanggal
            'C' => 15,  // Status
            'D' => 20,  // Shift
            'E' => 15,  // Jam Masuk
            'F' => 15,  // Jam Keluar
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Add title and info before the table
        $sheet->setCellValue('A1', 'JADWAL KERJA KARYAWAN');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);
        
        $sheet->setCellValue('A2', 'Nama Karyawan');
        $sheet->setCellValue('B2', ': ' . $this->employee->name);
        $sheet->setCellValue('A3', 'Periode');
        $sheet->setCellValue('B3', ': ' . $this->startDate->format('d M Y') . ' - ' . $this->endDate->format('d M Y'));
        
        // Get the count of rows in the exported data
        $lastRow = $sheet->getHighestRow();
        
        // Apply borders to all cells with data
        $sheet->getStyle("A5:F{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Header styling
        $headerStyle = [
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD'], // Blue shade for header
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ];

        // Apply header style to the header row
        $sheet->getStyle("A5:F5")->applyFromArray($headerStyle);

        // Content styling
        $contentStyle = [
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
                'wrapText' => true,
            ],
        ];

        // Apply content style to data rows
        $sheet->getStyle("A6:F{$lastRow}")->applyFromArray($contentStyle);

        // Specific column alignments
        $sheet->getStyle("A6:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // No
        $sheet->getStyle("B6:B{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Tanggal
        $sheet->getStyle("C6:C{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Status
        $sheet->getStyle("D6:D{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT); // Shift
        $sheet->getStyle("E6:E{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Jam Masuk
        $sheet->getStyle("F6:F{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Jam Keluar

        // Adjust row heights
        $sheet->getRowDimension(5)->setRowHeight(25); // Header row
        for ($i = 6; $i <= $lastRow; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(-1); // Auto height
        }

        return [];
    }
}