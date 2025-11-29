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
        $query = Incident::with([
            'vehicle', 
            'patient', 
            'dispatcher', 
            'transactions',
            'fromLocation',
            'toLocation',
            'drivers',
            'medicalStaff',
            'partner'
        ])->whereBetween('date', [$this->dateFrom, $this->dateTo]);

        if ($this->vehicleId) {
            $query->where('vehicle_id', $this->vehicleId);
        }

        // Sort by from_location name, then by date (oldest to newest)
        return $query->join('locations', 'incidents.from_location_id', '=', 'locations.id')
            ->select('incidents.*')
            ->orderBy('locations.name', 'asc')
            ->orderBy('incidents.date', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Số thứ tự',
            'Ngày',
            'Họ tên người bệnh',
            'Nơi đi',
            'Nơi đến',
            'Lái xe',
            'Nhân viên Y tế',
            'Ghi chú',
            'Hoa hồng',
            'Nơi nhận hoa hồng',
        ];
    }

    public function map($incident): array
    {
        // Get driver names
        $drivers = $incident->drivers->pluck('name')->join(', ') ?: '-';
        
        // Get medical staff names
        $medicalStaff = $incident->medicalStaff->pluck('name')->join(', ') ?: '-';
        
        // Get partner name for commission
        $partnerName = $incident->partner ? $incident->partner->name : '-';
        
        return [
            $incident->id,
            $incident->date->format('d/m/Y'),
            $incident->patient ? $incident->patient->name : '-',
            $incident->fromLocation ? $incident->fromLocation->name : '-',
            $incident->toLocation ? $incident->toLocation->name : '-',
            $drivers,
            $medicalStaff,
            $incident->summary ?? '-',
            $incident->commission_amount ? number_format($incident->commission_amount, 0, ',', '.') : '-',
            $partnerName,
        ];
    }

    public function title(): string
    {
        return 'Báo cáo khoa - phòng';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
