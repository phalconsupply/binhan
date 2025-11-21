<?php

namespace App\Exports;

use App\Models\Vehicle;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VehiclesReportExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
{
    protected $dateFrom;
    protected $dateTo;

    public function __construct($dateFrom, $dateTo)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function collection()
    {
        return Vehicle::withCount([
            'incidents' => function($q) {
                $q->whereBetween('date', [$this->dateFrom, $this->dateTo]);
            }
        ])->withSum([
            'transactions as revenue' => function($q) {
                $q->where('type', 'thu')->whereBetween('date', [$this->dateFrom, $this->dateTo]);
            }
        ], 'amount')
        ->withSum([
            'transactions as expense' => function($q) {
                $q->where('type', 'chi')->whereBetween('date', [$this->dateFrom, $this->dateTo]);
            }
        ], 'amount')
        ->orderBy('license_plate')
        ->get();
    }

    public function headings(): array
    {
        return [
            'Biển số',
            'Mẫu xe',
            'Tài xế',
            'SĐT',
            'Trạng thái',
            'Số chuyến',
            'Thu (đ)',
            'Chi (đ)',
            'Lợi nhuận (đ)',
        ];
    }

    public function map($vehicle): array
    {
        $revenue = $vehicle->revenue ?? 0;
        $expense = $vehicle->expense ?? 0;

        return [
            $vehicle->license_plate,
            $vehicle->model ?? '',
            $vehicle->driver_name ?? '',
            $vehicle->phone ?? '',
            $vehicle->status_label,
            $vehicle->incidents_count,
            $revenue,
            $expense,
            $revenue - $expense,
        ];
    }

    public function title(): string
    {
        return 'Báo cáo xe';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
