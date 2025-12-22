<?php

namespace App\Exports;

use App\Models\Incident;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class IncidentsIndexExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Incident::with(['patient', 'vehicle', 'dispatcher', 'transactions'])
            ->orderBy('date', 'desc');

        // Search filter (same as index page)
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function($q) use ($search) {
                $q->whereHas('vehicle', function($vq) use ($search) {
                    $vq->where('license_plate', 'like', "%{$search}%");
                })
                ->orWhereHas('patient', function($pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%")
                       ->orWhere('phone', 'like', "%{$search}%");
                })
                ->orWhere('incident_code', 'like', "%{$search}%")
                ->orWhere('pickup_location', 'like', "%{$search}%")
                ->orWhere('destination', 'like', "%{$search}%");
            });
        }

        // Filter by vehicle
        if (!empty($this->filters['vehicle_id'])) {
            $query->where('vehicle_id', $this->filters['vehicle_id']);
        }

        // Filter by owned vehicles (for vehicle owners)
        if (!empty($this->filters['owned_vehicle_ids'])) {
            $query->whereIn('vehicle_id', $this->filters['owned_vehicle_ids']);
        }

        // Filter by status
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        // Filter by date
        if (!empty($this->filters['date'])) {
            $query->whereDate('date', $this->filters['date']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'STT',
            'Mã chuyến',
            'Ngày',
            'Bệnh nhân',
            'Điện thoại',
            'Biển số xe',
            'Tài xế',
            'Điều phối viên',
            'Địa chỉ đón',
            'Địa chỉ đến',
            'Thu (đ)',
            'Chi (đ)',
            'Lợi nhuận (đ)',
            'Trạng thái',
            'Ghi chú'
        ];
    }

    public function map($incident): array
    {
        static $index = 0;
        $index++;

        // Calculate totals
        $revenue = $incident->transactions->where('type', 'thu')->sum('amount');
        $expense = $incident->transactions->where('type', 'chi')->sum('amount');
        $net = $revenue - $expense;

        // Status label
        $statusLabel = '';
        switch ($incident->status) {
            case 'pending':
                $statusLabel = 'Chờ xử lý';
                break;
            case 'confirmed':
                $statusLabel = 'Đã xác nhận';
                break;
            case 'in_progress':
                $statusLabel = 'Đang thực hiện';
                break;
            case 'completed':
                $statusLabel = 'Hoàn thành';
                break;
            case 'cancelled':
                $statusLabel = 'Đã hủy';
                break;
            default:
                $statusLabel = $incident->status;
        }

        return [
            $index,
            $incident->incident_code,
            $incident->date->format('d/m/Y H:i'),
            $incident->patient ? $incident->patient->name : '',
            $incident->patient ? $incident->patient->phone : '',
            $incident->vehicle ? $incident->vehicle->license_plate : '',
            $incident->vehicle ? $incident->vehicle->driver_name : '',
            $incident->dispatcher ? $incident->dispatcher->name : '',
            $incident->pickup_location ?? '',
            $incident->destination ?? '',
            $revenue,
            $expense,
            $net,
            $statusLabel,
            $incident->notes ?? ''
        ];
    }

    public function title(): string
    {
        return 'Danh sách chuyến đi';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0']
                ],
                'alignment' => ['horizontal' => 'center']
            ],
        ];
    }
}
