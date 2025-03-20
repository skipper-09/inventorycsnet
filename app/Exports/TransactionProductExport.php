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
        $query = TransactionProduct::with(['transaksi.customer', 'transaksi.branch', 'transaksi.toBranch', 'product.unit','transaksi.Transactiontechnition.user'])
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
                    'Dibuat',
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
                    'Produk',
                    'Dibuat',
                    'Teknisi Bertugas'
                ];
            case 'other':
                return [
                    'No',
                    'Tanggal',
                    'Nama Pekerjaan',
                    'Tujuan Transaksi',
                    'Produk',
                    'Dibuat',
                    'Teknisi Bertugas',
                ];
            default:
                return [
                    'No',
                    'Tanggal',
                    'Dari Cabang',
                    'Ke Cabang',
                    'Pekerjaan',
                    'Nama Customer',
                    'Alamat Customer',
                    'Tujuan Transaksi',
                    'Produk',
                    'Dibuat',
                    'Teknisi Bertugas',
                ];
        }
    }

    public function map($group): array
    {
        $firstRow = $group->first();
        // $technition = $group->map(function ($item) {
        //     $technicians = $item->transaksi->Transactiontechnition->map(function ($technition) {
        //         return "• " . ($technition->user->name ?? '-');
        //     });
        //     return $technicians->implode("\n");
        // })->implode("\n");

        $technicians = $firstRow->transaksi->Transactiontechnition->map(function ($technition) {
            return "• " . ($technition->user->name ?? '-');
        })->implode("\n");

        $products = $group->map(function ($item) {
            $unit = $item->product->unit->name ?? '';
            $snmodem = $item->product->is_modem == true ?  $item->product->transactionProduct->sn_modem : '';
            return "• {$item->product->name} ($snmodem) ({$item->quantity} {$unit})";
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
                    $firstRow->transaksi->userTransaction->name,
                    $products
                ];
            case 'psb':
                return [
                    $index,
                    $firstRow->created_at->format('d-m-Y H:i'),
                    $firstRow->transaksi->customer->name ?? '-',
                    $firstRow->transaksi->customer->address ?? '-',
                    $firstRow->getTransactionPurposeText(),
                    $products,
                    $firstRow->transaksi->userTransaction->name,
                    $technicians,
                ];
            case 'repair':
                return [
                    $index,
                    $firstRow->created_at->format('d-m-Y H:i'),
                    $firstRow->transaksi->customer->name ?? '-',
                    $firstRow->transaksi->customer->address ?? '-',
                    $firstRow->getTransactionPurposeText(),
                    $products,
                    $firstRow->transaksi->userTransaction->name,
                    $technicians,
                ];
            case 'other':
                return [
                    $index,
                    $firstRow->created_at->format('d-m-Y H:i'),
                    $firstRow->transaksi->WorkTransaction->name ?? '-',
                    $firstRow->getTransactionPurposeText(),
                    $products,
                    $firstRow->transaksi->userTransaction->name,
                    $technicians,
                ];
            default:
                return [
                    $index,
                    $firstRow->created_at->format('d-m-Y H:i'),
                    $firstRow->transaksi->branch->name ?? '-',
                    $firstRow->transaksi->toBranch->name ?? '-',
                    $firstRow->transaksi->WorkTransaction->name ?? '-',
                    $firstRow->transaksi->customer->name ?? '-',
                    $firstRow->transaksi->customer->address ?? '-',
                    $firstRow->getTransactionPurposeText(),
                    $products,
                    $firstRow->transaksi->userTransaction->name,
                    $technicians ?? '-',
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
                    'D' => 20, // Alamat Customer
                    'E' => 40, // Tujuan Transaksi
                    'F' => 50, // Produk
                ];
            case 'repair':
                return [
                    'A' => 5,  // No
                    'B' => 20, // Tanggal
                    'C' => 30, // Nama Customer
                    'D' => 20, // Alamat Customer
                    'E' => 40, // Tujuan Transaksi
                    'F' => 50, // Produk
                ];
            case 'other':
                return [
                    'A' => 5,  // No
                    'B' => 20, // Tanggal
                    'C' => 30, // Nama Customer
                    'D' => 20, // Tujuan Transaksi
                    'E' => 50, // Produk
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
                    'H' => 30,
                    'I' => 50,
                ];
        }
    }

    public function styles(Worksheet $sheet)
    {
        $headings = $this->headings();
        $lastColumnIndex = count($headings);
        $lastColumn = chr(64 + $lastColumnIndex);
        $lastRow = $sheet->getHighestRow();
    
        $sheet->getStyle("A1:{$lastColumn}{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
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
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD'], // Changed to a blue shade for better contrast
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
    
        // Content styling: Ensuring text is wrapped and aligned to the top
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
    
        // Specific column adjustments:
        // - Center align the 'No' column (Column A)
        // - Center align the 'Tanggal' column (Column B)
        // - Center align branch columns for 'transfer' type (Columns C and D)
        // - Left align customer name and address (Columns C and D for 'psb' and 'repair')
        $sheet->getStyle("A2:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("B2:B{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        if ($this->purpose === 'transfer') {
            $sheet->getStyle("C2:D{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        } elseif ($this->purpose === 'psb' || $this->purpose === 'repair') {
            $sheet->getStyle("C2:D{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        }
    
        // Adjust row height to improve readability, especially for multi-line text cells
        $sheet->getRowDimension(1)->setRowHeight(25); // Taller header row for readability
        for ($i = 2; $i <= $lastRow; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(-1); // Auto height based on content
        }
    
        return [];
    }
    
    
}
