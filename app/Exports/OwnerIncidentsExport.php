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

class OwnerIncidentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithColumnWidths
{
    protected $incidents;
    protected $vehicleName;
    protected $rowNumber = 0;

    public function __construct($incidents, $vehicleName = null)
    {
        $this->incidents = $incidents;
        $this->vehicleName = $vehicleName;
    }

    public function collection()
    {
        return $this->incidents;
    }

    public function headings(): array
    {
        return [
            'STT',
            'Ngày',
            'Chuyến',
            'Bệnh nhân',
            'GD',
            'Thu (đ)',
            'Chi (đ)',
            'Phí 15% (đ)',
            'Lợi nhuận (đ)',
        ];
    }

    public function map($group): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            \Carbon\Carbon::parse($group['date'])->format('d/m/Y'),
            '#' . $group['incident']->id,
            $group['incident']->patient->name ?? '-',
            $group['count'],
            number_format($group['total_revenue'], 0, ',', '.'),
            number_format($group['total_expense'], 0, ',', '.'),
            number_format($group['management_fee'] ?? 0, 0, ',', '.'),
            number_format($group['profit_after_fee'] ?? $group['net_amount'], 0, ',', '.'),
        ];
    }

    public function title(): string
    {
        return $this->vehicleName ? "Chuyến đi - {$this->vehicleName}" : 'Chuyến đi';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 12,
            'C' => 10,
            'D' => 25,
            'E' => 6,
            'F' => 15,
            'G' => 15,
            'H' => 15,
            'I' => 15,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->incidents->count() + 1;

        // Header style
        $sheet->getStyle('A1:I1')->applyFromArray([
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
        $sheet->getStyle("A2:I{$lastRow}")->applyFromArray([
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
        $sheet->getStyle("E2:I{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $sheet->getRowDimension(1)->setRowHeight(25);

        return [];
    }
}
