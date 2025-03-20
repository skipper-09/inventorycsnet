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
use Carbon\Carbon;

class AttendanceReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithColumnWidths
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
            'Jadwal',
            'Jam Masuk',
            'Status Masuk',
            'Jam Keluar',
            'Status Keluar',
            'Durasi Kerja',
        ];
    }

    public function map($attendance): array
    {
        static $index = 0;
        $index++;

        // Calculate work duration if both clock in and clock out exist
        $workDuration = 'N/A';
        
        if ($attendance->clock_in && $attendance->clock_out) {
            $clockIn = Carbon::parse($attendance->clock_in);
            $clockOut = Carbon::parse($attendance->clock_out);
            $diffInMinutes = $clockIn->diffInMinutes($clockOut);
            $diffInHours = floor($diffInMinutes / 60);
            $remainingMinutes = $diffInMinutes % 60;
            $workDuration = $diffInHours . ' jam ' . $remainingMinutes . ' menit';
        } else {
            $workDuration = 'Belum Checkout';
        }

        // Translate status
        $clockInStatus = $attendance->clock_in_status == 'late' ? 'Terlambat' : 'Tepat Waktu';
        $clockOutStatus = '';

        if ($attendance->clock_out) {
            $clockOutStatus = $attendance->clock_out_status == 'early' ? 'Pulang Awal' : 'Tepat Waktu';
        } else {
            $clockOutStatus = 'Belum Checkout';
        }

        return [
            $index,
            Carbon::parse($attendance->created_at)->format('d-M-Y'),
            $attendance->employee->name ?? 'N/A',
            $attendance->workSchedule->shift->name ?? 'N/A',
            $attendance->clock_in,
            $clockInStatus,
            $attendance->clock_out ?? 'Belum Checkout',
            $clockOutStatus,
            $workDuration,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 15,  // Tanggal
            'C' => 30,  // Nama Karyawan
            'D' => 20,  // Jadwal
            'E' => 15,  // Jam Masuk
            'F' => 15,  // Status Masuk
            'G' => 15,  // Jam Keluar
            'H' => 15,  // Status Keluar
            'I' => 20,  // Durasi Kerja
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
        $sheet->getStyle("D2:D{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT); // Jadwal
        $sheet->getStyle("E2:E{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Jam Masuk
        $sheet->getStyle("F2:F{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Status Masuk
        $sheet->getStyle("G2:G{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Jam Keluar
        $sheet->getStyle("H2:H{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Status Keluar
        $sheet->getStyle("I2:I{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Durasi Kerja

        // Adjust row height for better readability
        $sheet->getRowDimension(1)->setRowHeight(25); // Taller header row
        for ($i = 2; $i <= $lastRow; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(-1); // Auto height based on content
        }

        return [];
    }
}