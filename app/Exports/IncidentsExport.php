<?php

namespace App\Exports;

use App\Models\Incident;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class IncidentsExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
{
    protected $dateFrom;
    protected $dateTo;
    protected $vehicleId;

    public function __construct($dateFrom, $dateTo, $vehicleId = null)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->vehicleId = $vehicleId;
    }

    public function collection()
    {
        $query = Incident::with(['vehicle', 'patient', 'dispatcher', 'transactions'])
            ->whereBetween('date', [$this->dateFrom, $this->dateTo]);

        if ($this->vehicleId) {
            $query->where('vehicle_id', $this->vehicleId);
        }

        return $query->orderBy('date', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Ngày giờ',
            'Biển số xe',
            'Bệnh nhân',
            'SĐT',
            'Điểm đến',
            'Điều phối bởi',
            'Thu (đ)',
            'Chi (đ)',
            'Lợi nhuận (đ)',
            'Ghi chú',
        ];
    }

    public function map($incident): array
    {
        return [
            $incident->id,
            $incident->date->format('d/m/Y H:i'),
            $incident->vehicle->license_plate,
            $incident->patient ? $incident->patient->name : '',
            $incident->patient ? $incident->patient->phone : '',
            $incident->destination ?? '',
            $incident->dispatcher->name,
            $incident->total_revenue,
            $incident->total_expense,
            $incident->net_amount,
            $incident->summary ?? '',
        ];
    }

    public function title(): string
    {
        return 'Chuyến đi';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
