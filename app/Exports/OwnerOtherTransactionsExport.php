<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class OwnerOtherTransactionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithColumnWidths
{
    protected $transactions;
    protected $vehicleName;
    protected $rowNumber = 0;

    public function __construct($transactions, $vehicleName = null)
    {
        $this->transactions = $transactions;
        $this->vehicleName = $vehicleName;
    }

    public function collection()
    {
        return $this->transactions;
    }

    public function headings(): array
    {
        return [
            'STT',
            'Ngày',
            'Mã GD',
            'Loại',
            'Danh mục',
            'Ghi chú',
            'Thu (đ)',
            'Chi (đ)',
        ];
    }

    public function map($group): array
    {
        $this->rowNumber++;
        $transaction = $group['transactions']->first();
        $isRevenue = in_array($transaction->type, ['thu', 'vay_cong_ty', 'nop_quy']);

        // Map type to Vietnamese
        $typeLabels = [
            'vay_cong_ty' => 'Vay công ty',
            'tra_cong_ty' => 'Trả công ty',
            'nop_quy' => 'Nộp quỹ',
            'thu' => 'Thu khác',
            'chi' => 'Chi khác',
        ];

        return [
            $this->rowNumber,
            \Carbon\Carbon::parse($transaction->date)->format('d/m/Y'),
            $transaction->transaction_code ?? '-',
            $typeLabels[$transaction->type] ?? $transaction->type,
            $transaction->category ?? '-',
            $transaction->notes ?? '-',
            $isRevenue ? $transaction->amount : 0, // Raw number for Excel
            !$isRevenue ? $transaction->amount : 0, // Raw number for Excel
        ];
    }

    public function title(): string
    {
        return $this->vehicleName ? "Giao dịch khác - {$this->vehicleName}" : 'Giao dịch khác';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 12,
            'C' => 12,
            'D' => 15,
            'E' => 20,
            'F' => 30,
            'G' => 15,
            'H' => 15,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->transactions->count() + 1;

        // Header style
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Data style
        $sheet->getStyle("A2:H{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Right align numbers
        $sheet->getStyle("G2:H{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Format currency columns G, H with thousand separator
        $sheet->getStyle("G2:H{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');

        $sheet->getRowDimension(1)->setRowHeight(25);

        return [];
    }
}
