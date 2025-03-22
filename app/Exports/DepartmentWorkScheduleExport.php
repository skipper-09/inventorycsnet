<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DepartmentWorkScheduleExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithColumnWidths, WithTitle, WithCustomStartCell
{
    protected $schedules;
    protected $department;
    protected $employees;
    protected $startDate;
    protected $endDate;
    protected $dates = [];

    public function __construct($schedules, $department, $employees, $startDate, $endDate)
    {
        $this->schedules = $schedules;
        $this->department = $department;
        $this->employees = $employees;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        
        // Generate array of dates between start and end date
        $this->generateDates();
    }

    private function generateDates()
    {
        $currentDate = clone $this->startDate;
        while ($currentDate->lte($this->endDate)) {
            $this->dates[] = clone $currentDate;
            $currentDate->addDay();
        }
    }

    public function collection()
    {
        $data = new Collection();
        
        // Loop through each employee
        foreach ($this->employees as $index => $employee) {
            $row = [
                'no' => $index + 1,
                'name' => $employee->name
            ];
            
            // Add schedule for each date
            foreach ($this->dates as $date) {
                $dateString = $date->format('Y-m-d');
                $schedule = $this->schedules->first(function ($item) use ($employee, $dateString) {
                    return $item->employee_id == $employee->id && $item->schedule_date == $dateString;
                });
                
                $status = '-';
                $shift = '-';
                $timeIn = '-';
                $timeOut = '-';
                
                if ($schedule) {
                    if ($schedule->status == 'work') {
                        $status = 'Kerja';
                        $shift = optional($schedule->shift)->name ?? '-';
                        $timeIn = optional($schedule->shift)->shift_start ?? '-';
                        $timeOut = optional($schedule->shift)->shift_end ?? '-';
                    } else {
                        $status = 'Libur';
                    }
                }
                
                // Add all details as a single formatted string to avoid complex merging
                $row[$date->format('d-M-Y')] = $status . "\n" . 
                                              ($status == 'Kerja' ? "Shift: " . $shift . "\n" .
                                               "In: " . $timeIn . "\n" .
                                               "Out: " . $timeOut : "");
            }
            
            $data->push($row);
        }
        
        return $data;
    }

    public function title(): string
    {
        return 'Jadwal Kerja Departemen';
    }

    public function startCell(): string
    {
        return 'A5';
    }

    public function headings(): array
    {
        $headings = [
            'No',
            'Nama Karyawan'
        ];
        
        // Add date headings
        foreach ($this->dates as $date) {
            // Create a formatted date string with day of week
            $dateFormat = $date->format('d-M');
            $dayName = $date->locale('id')->isoFormat('ddd'); // 'Sen', 'Sel', etc.
            $headings[] = $dateFormat . ' ' . $dayName;
        }
        
        return $headings;
    }

    public function columnWidths(): array
    {
        $widths = [
            'A' => 5,   // No
            'B' => 30,  // Nama Karyawan
        ];
        
        // Set width for date columns
        $column = 'C';
        foreach ($this->dates as $date) {
            $widths[$column] = 18; // Width for date columns
            $column++;
        }
        
        return $widths;
    }

    public function styles(Worksheet $sheet)
    {
        // Calculate last column letter based on the dates
        $colCount = count($this->dates);
        $lastColumnIndex = 2 + $colCount; // 0=A, 1=B, 2=C, so we add 2 for existing columns + dates
        $lastColumn = $this->getColumnLetterFromIndex($lastColumnIndex);
        
        // Add title and info before the table
        $sheet->setCellValue('A1', 'JADWAL KERJA DEPARTEMEN');
        $sheet->mergeCells("A1:{$lastColumn}1");
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => '1F4E78'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'DCE6F1'],
            ],
        ]);
        
        // Make row 1 taller
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Style for company info
        $infoStyle = [
            'font' => [
                'bold' => true,
                'size' => 11,
            ],
        ];
        
        $sheet->setCellValue('A2', 'Departemen');
        $sheet->setCellValue('B2', ': ' . $this->department->name);
        $sheet->setCellValue('A3', 'Periode');
        $sheet->setCellValue('B3', ': ' . $this->startDate->format('d M Y') . ' - ' . $this->endDate->format('d M Y'));
        $sheet->setCellValue('A4', 'Jumlah Karyawan');
        $sheet->setCellValue('B4', ': ' . $this->employees->count() . ' orang');
        
        // Apply info style
        $sheet->getStyle('A2:A4')->applyFromArray($infoStyle);
        $sheet->mergeCells("B2:{$lastColumn}2");
        $sheet->mergeCells("B3:{$lastColumn}3");
        $sheet->mergeCells("B4:{$lastColumn}4");

        // Get the count of rows in the exported data
        $rowCount = count($this->employees);
        $lastRow = 5 + $rowCount;

        // Apply borders to all cells with data
        $sheet->getStyle("A5:{$lastColumn}{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '9BAEC8'],
                ],
            ],
        ]);

        // Header styling
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD'],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'wrapText' => true,
            ],
        ];

        // Apply header style to the header row
        $sheet->getStyle("A5:{$lastColumn}5")->applyFromArray($headerStyle);

        // Content styling - base style for all content
        $contentStyle = [
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
                'wrapText' => true,
            ],
        ];

        // Apply content style to data rows
        $sheet->getStyle("A6:{$lastColumn}{$lastRow}")->applyFromArray($contentStyle);

        // Specific column alignments
        $sheet->getStyle("A6:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // No
        $sheet->getStyle("B6:B{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT); // Nama Karyawan
        
        // Zebra striping for better readability
        for ($i = 6; $i <= $lastRow; $i++) {
            if ($i % 2 == 0) {
                $sheet->getStyle("A{$i}:{$lastColumn}{$i}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->setStartColor(new Color('F5F5F5'));
            }
        }
        
        // Style weekend date columns with a different color
        for ($i = 0; $i < count($this->dates); $i++) {
            $date = $this->dates[$i];
            $colIndex = 2 + $i; // 0-based index, A=0, B=1, C=2, etc.
            $colLetter = $this->getColumnLetterFromIndex($colIndex);
            
            // Weekend highlighting
            $isWeekend = in_array($date->format('N'), [6, 7]); // 6=Saturday, 7=Sunday
            if ($isWeekend) {
                $sheet->getStyle("{$colLetter}5")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->setStartColor(new Color('FF9900')); // Orange for weekend headers
                
                // Make the weekend column data also slightly highlighted
                $sheet->getStyle("{$colLetter}6:{$colLetter}{$lastRow}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->setStartColor(new Color('FFEBCC')); // Light orange for weekend data
            }
            
            // Apply the format for date columns data
            $sheet->getStyle("{$colLetter}6:{$colLetter}{$lastRow}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // Apply conditional formatting for "Kerja" and "Libur" statuses
        for ($row = 6; $row <= $lastRow; $row++) {
            for ($i = 0; $i < count($this->dates); $i++) {
                $colIndex = 2 + $i; // C is index 2
                $colLetter = $this->getColumnLetterFromIndex($colIndex);
                
                $cellValue = $sheet->getCell("{$colLetter}{$row}")->getValue();
                
                if (strpos($cellValue, 'Kerja') === 0) {
                    // Green text for "Kerja"
                    $sheet->getStyle("{$colLetter}{$row}")->getFont()->setColor(new Color('006600'));
                } elseif (strpos($cellValue, 'Libur') === 0) {
                    // Red text for "Libur"
                    $sheet->getStyle("{$colLetter}{$row}")->getFont()->setColor(new Color('CC0000'));
                }
            }
        }

        // Adjust row heights
        $sheet->getRowDimension(5)->setRowHeight(40); // Header row taller for wrapped text
        for ($i = 6; $i <= $lastRow; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(90); // Content rows - taller to accommodate multiple lines
        }

        // Add a border around the entire table area
        $sheet->getStyle("A1:{$lastColumn}{$lastRow}")->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '4F81BD'],
                ],
            ],
        ]);

        return [];
    }
    
    /**
     * Convert a 0-based column index to a letter (A, B, C, ... Z, AA, AB, etc.)
     * 
     * @param int $index The 0-based column index
     * @return string The column letter
     */
    private function getColumnLetterFromIndex($index)
    {
        $letter = '';
        while ($index >= 0) {
            $letter = chr(65 + ($index % 26)) . $letter;
            $index = floor($index / 26) - 1;
        }
        return $letter;
    }
}