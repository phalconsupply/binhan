<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
{
    protected $dateFrom;
    protected $dateTo;
    protected $vehicleId;
    protected $type;

    public function __construct($dateFrom, $dateTo, $vehicleId = null, $type = null)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->vehicleId = $vehicleId;
        $this->type = $type;
    }

    public function collection()
    {
        $query = Transaction::with(['vehicle', 'incident.patient', 'recorder'])
            ->whereBetween('date', [$this->dateFrom, $this->dateTo]);

        if ($this->vehicleId) {
            $query->where('vehicle_id', $this->vehicleId);
        }

        if ($this->type) {
            $query->where('type', $this->type);
        }

        return $query->orderBy('date', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Ngày',
            'Loại',
            'Biển số xe',
            'Bệnh nhân',
            'Số tiền (đ)',
            'Phương thức',
            'Ghi nhận bởi',
            'Ghi chú',
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->id,
            $transaction->date->format('d/m/Y H:i'),
            $transaction->type_label,
            $transaction->vehicle->license_plate,
            $transaction->incident && $transaction->incident->patient ? $transaction->incident->patient->name : '',
            $transaction->amount,
            $transaction->method_label,
            $transaction->recorder->name,
            $transaction->note ?? '',
        ];
    }

    public function title(): string
    {
        return 'Giao dịch';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
