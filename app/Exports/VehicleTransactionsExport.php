<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VehicleTransactionsExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize
{
    protected $vehicleId;
    protected $filters;

    public function __construct($vehicleId, $filters = [])
    {
        $this->vehicleId = $vehicleId;
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Transaction::where('vehicle_id', $this->vehicleId)
            ->with(['incident.patient', 'vehicle'])
            ->orderBy('date', 'desc');

        // Filter by date range
        if (!empty($this->filters['date_from'])) {
            $query->where('date', '>=', $this->filters['date_from']);
        }
        if (!empty($this->filters['date_to'])) {
            $query->where('date', '<=', $this->filters['date_to']);
        }

        // Filter by transaction type
        if (!empty($this->filters['transaction_type'])) {
            switch ($this->filters['transaction_type']) {
                case 'chuyen':
                    // Giao dịch có incident_id (chuyến xe)
                    $query->whereNotNull('incident_id');
                    break;
                case 'nop_quy':
                    // Nộp quỹ
                    $query->where('type', 'nop_quy');
                    break;
                case 'khac':
                    // Giao dịch khác (không có incident, không phải nộp quỹ)
                    $query->whereNull('incident_id')
                        ->where('type', '!=', 'nop_quy');
                    break;
            }
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'STT',
            'Ngày',
            'Loại',
            'Mã chuyến',
            'Bệnh nhân',
            'Danh mục',
            'Mô tả',
            'Thu (đ)',
            'Chi (đ)',
            'Ghi chú'
        ];
    }

    public function map($transaction): array
    {
        static $index = 0;
        $index++;

        // Determine type label
        $typeLabel = '';
        if ($transaction->type === 'nop_quy') {
            $typeLabel = 'Nộp quỹ';
        } elseif ($transaction->incident_id) {
            $typeLabel = 'Chuyến xe';
        } else {
            $typeLabel = 'Khác';
        }

        // Get incident code and patient
        $incidentCode = $transaction->incident ? $transaction->incident->incident_code : '';
        $patientName = $transaction->incident && $transaction->incident->patient 
            ? $transaction->incident->patient->name 
            : '';

        // Get category label
        $categoryLabel = '';
        switch ($transaction->category) {
            case 'doanh_thu':
                $categoryLabel = 'Doanh thu';
                break;
            case 'xăng_dầu':
                $categoryLabel = 'Xăng dầu';
                break;
            case 'bảo_trì':
                $categoryLabel = 'Bảo trì';
                break;
            case 'bảo_trì_xe_chủ_riêng':
                $categoryLabel = 'Bảo trì xe chủ riêng';
                break;
            case 'lương_nhân_viên':
                $categoryLabel = 'Lương nhân viên';
                break;
            case 'phí_hành_chính':
                $categoryLabel = 'Phí hành chính';
                break;
            case 'nop_quy':
                $categoryLabel = 'Nộp quỹ';
                break;
            case 'chi_khác':
                $categoryLabel = 'Chi khác';
                break;
            default:
                $categoryLabel = $transaction->category;
        }

        // Calculate revenue and expense
        $revenue = 0;
        $expense = 0;
        
        if ($transaction->type === 'thu' || $transaction->type === 'nop_quy') {
            $revenue = $transaction->amount;
        } else {
            $expense = $transaction->amount;
        }

        return [
            $index,
            $transaction->date->format('d/m/Y'),
            $typeLabel,
            $incidentCode,
            $patientName,
            $categoryLabel,
            $transaction->description,
            $revenue,
            $expense,
            $transaction->notes ?? ''
        ];
    }

    public function title(): string
    {
        return 'Giao dịch';
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
