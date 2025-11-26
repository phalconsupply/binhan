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

class VehicleMaintenancesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, WithColumnWidths
{
    protected $maintenances;
    protected $totalCost;
    protected $rowNumber = 0;

    public function __construct($maintenances, $totalCost)
    {
        $this->maintenances = $maintenances;
        $this->totalCost = $totalCost;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->maintenances;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'STT',
            'Ngày',
            'Biển số xe',
            'Dịch vụ',
            'Đối tác',
            'Chi phí (đ)',
            'Số km',
            'Mô tả',
            'Người tạo',
        ];
    }

    /**
     * @param mixed $maintenance
     * @return array
     */
    public function map($maintenance): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $maintenance->date->format('d/m/Y'),
            $maintenance->vehicle->license_plate,
            $maintenance->maintenanceService->name ?? '-',
            $maintenance->partner->name ?? '-',
            number_format($maintenance->cost, 0, ',', '.'),
            $maintenance->mileage ? number_format($maintenance->mileage, 0, ',', '.') : '-',
            $maintenance->description ?? '-',
            $maintenance->user->name,
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Danh sách bảo trì xe';
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 6,   // STT
            'B' => 12,  // Ngày
            'C' => 15,  // Biển số xe
            'D' => 25,  // Dịch vụ
            'E' => 25,  // Đối tác
            'F' => 15,  // Chi phí
            'G' => 12,  // Số km
            'H' => 30,  // Mô tả
            'I' => 20,  // Người tạo
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->maintenances->count() + 1;

        // Title row
        $sheet->insertNewRowBefore(1, 2);
        $sheet->setCellValue('A1', 'DANH SÁCH BẢO TRÌ XE');
        $sheet->setCellValue('A2', 'Ngày xuất: ' . now()->format('d/m/Y H:i'));
        $sheet->mergeCells('A1:I1');
        $sheet->mergeCells('A2:I2');

        // Summary row
        $sheet->setCellValue('A' . ($lastRow + 3), 'TỔNG CHI PHÍ:');
        $sheet->setCellValue('F' . ($lastRow + 3), number_format($this->totalCost, 0, ',', '.') . ' đ');
        $sheet->mergeCells('A' . ($lastRow + 3) . ':E' . ($lastRow + 3));

        return [
            // Title style
            1 => [
                'font' => ['bold' => true, 'size' => 16],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            2 => [
                'font' => ['italic' => true, 'size' => 11],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            // Header style
            3 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0'],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            // Data rows
            'A4:I' . $lastRow => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ],
            // Summary row
            $lastRow + 3 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FEF3C7'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
            ],
            // Right align for cost column
            'F4:F' . $lastRow => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            ],
            // Center align for STT and date
            'A4:B' . $lastRow => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}
