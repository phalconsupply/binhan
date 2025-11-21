<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Báo cáo Chuyến đi</title>
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
        <h2>BÁO CÁO CHUYẾN ĐI</h2>
        <p>Từ ngày {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} đến {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</p>
    </div>

    <div class="summary">
        <strong>Tổng kết:</strong><br>
        Tổng số chuyến: {{ $totals['count'] }}<br>
        Tổng thu: {{ number_format($totals['revenue'], 0, ',', '.') }}đ<br>
        Tổng chi: {{ number_format($totals['expense'], 0, ',', '.') }}đ<br>
        Lợi nhuận: <span class="{{ $totals['net'] >= 0 ? 'text-green' : 'text-red' }}">{{ number_format($totals['net'], 0, ',', '.') }}đ</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>Ngày</th>
                <th>Xe</th>
                <th>Bệnh nhân</th>
                <th>Điểm đến</th>
                <th class="text-right">Thu</th>
                <th class="text-right">Chi</th>
                <th class="text-right">Lợi nhuận</th>
            </tr>
        </thead>
        <tbody>
            @foreach($incidents as $incident)
            <tr>
                <td>{{ $incident->date->format('d/m/Y H:i') }}</td>
                <td>{{ $incident->vehicle->license_plate }}</td>
                <td>{{ $incident->patient ? $incident->patient->name : '-' }}</td>
                <td>{{ $incident->destination ?? '-' }}</td>
                <td class="text-right">{{ number_format($incident->total_revenue, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($incident->total_expense, 0, ',', '.') }}</td>
                <td class="text-right {{ $incident->net_amount >= 0 ? 'text-green' : 'text-red' }}">
                    {{ number_format($incident->net_amount, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p style="margin-top: 30px; text-align: right;">
        In ngày: {{ now()->format('d/m/Y H:i') }}
    </p>
</body>
</html>
