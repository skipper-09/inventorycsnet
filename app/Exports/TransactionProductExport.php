<?php

namespace App\Exports;

use App\Models\TransactionProduct;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TransactionProductExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithColumnWidths
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = TransactionProduct::with(['transaksi', 'product.unit'])
            ->whereHas('transaksi', function ($query) {
                $query->where('type', 'out')
                    ->where('purpose', '!=', 'stock_in');
            })
            ->orderByDesc('created_at');

        // Apply transaction purpose filter
        if ($this->request->filled('transaksi')) {
            $query->whereHas('transaksi', function ($q) {
                $q->where('purpose', $this->request->input('transaksi'))
                    ->where('purpose', '!=', 'stock_in');
            });
        }

        // Apply date filter
        if ($this->request->filled('created_at')) {
            $query->whereDate('created_at', $this->request->input('created_at'));
        }

        return $query->get()->groupBy('transaksi.id');
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Tujuan Transaksi',
            'Produk'
        ];
    }

    public function map($group): array
    {
        $firstRow = $group->first();
        
        // Format products with bullet points and new lines
        $products = $group->map(function ($item) {
            $unit = $item->product->unit->name ?? '';
            return "• {$item->product->name} ({$item->quantity} {$unit})";
        })->implode("\n");

        static $index = 0;
        $index++;

        return [
            $index,
            $firstRow->created_at->format('d-m-Y H:i'),
            $firstRow->getTransactionPurposeText(),
            $products
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,  // No
            'B' => 20, // Tanggal
            'C' => 20, // Tujuan Transaksi
            'D' => 50, // Produk
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        
        // Style for all cells
        $sheet->getStyle('A1:D' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // Header styling
        $headerStyle = [
            'font' => [
                'bold' => true,
                'size' => 11,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'CCCCCC']
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ];

        // Content styling
        $contentStyle = [
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
                'wrapText' => true,
            ],
        ];

        // Apply styles
        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);
        $sheet->getStyle('A2:D' . $lastRow)->applyFromArray($contentStyle);
        
        // Center align the number and date columns
        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B2:B' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Set row height for header
        $sheet->getRowDimension(1)->setRowHeight(20);
        
        // Set minimum row height for content
        for ($i = 2; $i <= $lastRow; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(-1); // Auto height
        }

        return [];
    }
}