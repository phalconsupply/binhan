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

class OwnerMaintenancesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithColumnWidths
{
    protected $maintenances;
    protected $vehicleName;
    protected $rowNumber = 0;

    public function __construct($maintenances, $vehicleName = null)
    {
        $this->maintenances = $maintenances;
        $this->vehicleName = $vehicleName;
    }

    public function collection()
    {
        return $this->maintenances;
    }

    public function headings(): array
    {
        return [
            'STT',
            'Ngày',
            'Dịch vụ',
            'Đối tác',
            'GD',
            'Chi phí (đ)',
        ];
    }

    public function map($group): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            \Carbon\Carbon::parse($group['date'])->format('d/m/Y'),
            $group['maintenance']->maintenanceService->name ?? '-',
            $group['maintenance']->partner->name ?? '-',
            $group['count'],
            $group['total_expense'], // Raw number for Excel
        ];
    }

    public function title(): string
    {
        return $this->vehicleName ? "Bảo trì xe - {$this->vehicleName}" : 'Bảo trì xe';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 12,
            'C' => 25,
            'D' => 25,
            'E' => 6,
            'F' => 15,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->maintenances->count() + 1;

        // Header style
        $sheet->getStyle('A1:F1')->applyFromArray([
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
        $sheet->getStyle("A2:F{$lastRow}")->applyFromArray([
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
        $sheet->getStyle("E2:F{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Format currency column F with thousand separator
        $sheet->getStyle("F2:F{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');

        $sheet->getRowDimension(1)->setRowHeight(25);

        return [];
    }
}
