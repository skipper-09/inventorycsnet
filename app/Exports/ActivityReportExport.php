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

class ActivityReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithColumnWidths
{
    protected $data;
    protected $userRole;

    public function __construct($data, $userRole)
    {
        $this->data = $data;
        $this->userRole = $userRole;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        if ($this->userRole == 'Employee') {
            return [
                'No',
                'Laporan Aktivitas',
                'Tanggal',
            ];
        } else {
            return [
                'No',
                'Nama',
                'Jabatan',
                'Laporan Aktivitas',
                'Tanggal',
            ];
        }
    }

    public function map($freeReport): array
    {
        static $index = 0;
        $index++;

        $cleanReport = strip_tags($freeReport->report_activity);
        
        if ($this->userRole == 'Employee') {
            return [
                $index,
                $cleanReport,
                formatDate($freeReport->created_at),
            ];
        } else {
            return [
                $index,
                $freeReport->user->name ?? '-',
                $freeReport->user->employee->position->name ?? '-',
                $cleanReport,
                formatDate($freeReport->created_at),
            ];
        }
    }

    public function columnWidths(): array
    {
        if ($this->userRole == 'Employee') {
            return [
                'A' => 5,   // No
                'B' => 100, // Laporan Aktivitas
                'C' => 20,  // Tanggal
            ];
        } else {
            return [
                'A' => 5,   // No
                'B' => 25,  // Nama
                'C' => 25,  // Jabatan
                'D' => 80,  // Laporan Aktivitas
                'E' => 20,  // Tanggal
            ];
        }
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
        
        if ($this->userRole == 'Employee') {
            $sheet->getStyle("C2:C{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Date column
        } else {
            $sheet->getStyle("B2:C{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT); // Name and Position
            $sheet->getStyle("E2:E{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Date column
        }
    
        // Adjust row height for better readability
        $sheet->getRowDimension(1)->setRowHeight(25); // Taller header row
        for ($i = 2; $i <= $lastRow; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(-1); // Auto height based on content
        }
    
        return [];
    }
}