<?php

namespace App\Exports;

use App\Models\TransactionProduct;
use Illuminate\Support\Facades\Log;
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
    protected $purpose;

    public function __construct($request)
    {
        $this->request = $request;
        $this->purpose = $request->input('type_transaction');
    }

    public function collection()
    {
        $query = TransactionProduct::with(['transaksi.customer', 'transaksi.branch', 'transaksi.toBranch', 'product.unit'])
            ->whereHas('transaksi', function ($query) {
                $query->where('type', 'out')
                    ->where('purpose', '!=', 'stock_in');
            });

        if ($this->request->filled('type_transaction')) {
            $query->whereHas('transaksi', function ($q) {
                $q->where('purpose', $this->request->input('type_transaction'));
            });
        }

        // Filter berdasarkan rentang tanggal
        if ($this->request->filled('start_date') && $this->request->filled('end_date')) {
            $query->whereDate('created_at', '>=', $this->request->input('start_date'))
                  ->whereDate('created_at', '<=', $this->request->input('end_date'));
        }
        // elseif ($this->request->filled('start_date')) {
        //     $query->whereDate('created_at', '>=', $this->request->input('start_date'));
        // } elseif ($this->request->filled('end_date')) {
        //     $query->whereDate('created_at', '<=', $this->request->input('end_date'));
        // }

        return $query->get()->groupBy('transaksi.id');
    }



    public function headings(): array
    {
        switch ($this->purpose) {
            case 'transfer':
                return [
                    'No',
                    'Tanggal',
                    'Dari Cabang',
                    'Ke Cabang',
                    'Tujuan Transaksi',
                    'Produk'
                ];
            case 'psb':
            case 'repair':
                return [
                    'No',
                    'Tanggal',
                    'Nama Customer',
                    'Alamat Customer',
                    'Tujuan Transaksi',
                    'Produk'
                ];
            default:
                return [
                    'No',
                    'Tanggal',
                    'Dari Cabang',
                    'Ke Cabang',
                    'Nama Customer',
                    'Alamat Customer',
                    'Tujuan Transaksi',
                    'Produk'
                ];
        }
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

        switch ($this->purpose) {
            case 'transfer':
                return [
                    $index,
                    $firstRow->created_at->format('d-m-Y H:i'),
                    $firstRow->transaksi->branch->name ?? '-',
                    $firstRow->transaksi->toBranch->name ?? '-',
                    $firstRow->getTransactionPurposeText(),
                    $products
                ];
            case 'psb':
                return [
                    $index,
                    $firstRow->created_at->format('d-m-Y H:i'),
                    $firstRow->transaksi->customer->name ?? '-',
                    $firstRow->transaksi->customer->address ?? '-',
                    $firstRow->getTransactionPurposeText(),
                    $products
                ];
            default:
                return [
                    $index,
                    $firstRow->created_at->format('d-m-Y H:i'),
                    $firstRow->transaksi->branch->name ?? '-',
                    $firstRow->transaksi->toBranch->name ?? '-',
                    $firstRow->transaksi->customer->name ?? '-',
                    $firstRow->transaksi->customer->address ?? '-',
                    $firstRow->getTransactionPurposeText(),
                    $products
                ];
        }
    }

    public function columnWidths(): array
    {
        switch ($this->purpose) {
            case 'transfer':
                return [
                    'A' => 5,  // No
                    'B' => 20, // Tanggal
                    'C' => 25, // Dari Cabang
                    'D' => 25, // Ke Cabang
                    'E' => 20, // Tujuan Transaksi
                    'F' => 50, // Produk
                ];
            case 'psb':
                return [
                    'A' => 5,  // No
                    'B' => 20, // Tanggal
                    'C' => 30, // Nama Customer
                    'D' => 40, // Alamat Customer
                    'E' => 20, // Tujuan Transaksi
                    'F' => 50, // Produk
                ];
            default:
                return [
                    'A' => 5,
                    'B' => 20,
                    'C' => 25,
                    'D' => 25,
                    'E' => 20,
                    'F' => 50,
                    'G' => 50,
                    'H' => 50,
                ];
        }
    }

    public function styles(Worksheet $sheet)
    {
        $lastColumn = in_array($this->purpose, ['transfer', 'psb']) ? 'F' : 'H'; // Sesuaikan dengan kolom terakhir yang relevan
        $lastRow = $sheet->getHighestRow();

        // Style for all cells
        $sheet->getStyle("A1:{$lastColumn}{$lastRow}")->applyFromArray([
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
        $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray($headerStyle);
        $sheet->getStyle("A2:{$lastColumn}{$lastRow}")->applyFromArray($contentStyle);

        // Center align specific columns
        $sheet->getStyle("A2:B{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        if ($this->purpose === 'transfer') {
            // Center align branch columns for transfer
            $sheet->getStyle("C2:D{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        } elseif ($this->purpose === 'psb') {
            // Left align customer name and address
            $sheet->getStyle("C2:D{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        }

        // Set row height for header
        $sheet->getRowDimension(1)->setRowHeight(20);

        // Set minimum row height for content
        for ($i = 2; $i <= $lastRow; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(-1); // Auto height
        }

        return [];
    }
}
