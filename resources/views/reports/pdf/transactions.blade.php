<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Báo cáo Giao dịch</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .header { text-align: center; margin-bottom: 20px; }
        .summary { margin: 20px 0; padding: 10px; background-color: #f9f9f9; }
        .text-right { text-align: right; }
        .text-green { color: green; }
        .text-red { color: red; }
    </style>
</head>
<body>
    <div class="header">
        <h2>BÁO CÁO GIAO DỊCH</h2>
        <p>Từ ngày {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} đến {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</p>
    </div>

    <div class="summary">
        <strong>Tổng kết:</strong><br>
        Tổng số giao dịch: {{ $totals['count'] }}<br>
        Tổng thu: {{ number_format($totals['revenue'], 0, ',', '.') }}đ<br>
        Tổng chi: {{ number_format($totals['expense'], 0, ',', '.') }}đ<br>
        Lợi nhuận: <span class="{{ $totals['net'] >= 0 ? 'text-green' : 'text-red' }}">{{ number_format($totals['net'], 0, ',', '.') }}đ</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>Ngày</th>
                <th>Loại</th>
                <th>Xe</th>
                <th>Bệnh nhân</th>
                <th class="text-right">Số tiền</th>
                <th>Phương thức</th>
                <th>Ghi chú</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
            <tr>
                <td>{{ $transaction->date->format('d/m/Y') }}</td>
                <td>{{ $transaction->type_label }}</td>
                <td>{{ $transaction->vehicle->license_plate }}</td>
                <td>{{ $transaction->incident && $transaction->incident->patient ? $transaction->incident->patient->name : '-' }}</td>
                <td class="text-right {{ $transaction->type == 'thu' ? 'text-green' : 'text-red' }}">
                    {{ number_format($transaction->amount, 0, ',', '.') }}
                </td>
                <td>{{ $transaction->method_label }}</td>
                <td>{{ $transaction->note ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p style="margin-top: 30px; text-align: right;">
        In ngày: {{ now()->format('d/m/Y H:i') }}
    </p>
</body>
</html>
