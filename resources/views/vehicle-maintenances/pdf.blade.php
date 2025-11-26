<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Danh sách bảo trì xe</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .header .date {
            margin-top: 5px;
            font-size: 11px;
            font-style: italic;
        }
        .summary {
            background-color: #FEF3C7;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .summary-label {
            font-weight: bold;
            font-size: 14px;
        }
        .summary-value {
            font-weight: bold;
            font-size: 16px;
            color: #D97706;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #E2E8F0;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #CBD5E0;
        }
        td {
            padding: 6px 8px;
            border: 1px solid #E2E8F0;
        }
        tr:nth-child(even) {
            background-color: #F7FAFC;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .cost {
            font-weight: 600;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #718096;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>DANH SÁCH BẢO TRÌ XE</h1>
        @if(isset($vehicle))
            <div class="date">Xe: {{ $vehicle->license_plate }}</div>
        @endif
        <div class="date">Ngày xuất: {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <div class="summary">
        <span class="summary-label">TỔNG CHI PHÍ:</span>
        <span class="summary-value">{{ number_format($totalCost, 0, ',', '.') }} đ</span>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="5%">STT</th>
                <th class="text-center" width="10%">Ngày</th>
                <th width="12%">Biển số xe</th>
                <th width="20%">Dịch vụ</th>
                <th width="18%">Đối tác</th>
                <th class="text-right" width="13%">Chi phí (đ)</th>
                <th class="text-center" width="10%">Số km</th>
                <th width="12%">Người tạo</th>
            </tr>
        </thead>
        <tbody>
            @forelse($maintenances as $index => $maintenance)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $maintenance->date->format('d/m/Y') }}</td>
                <td>{{ $maintenance->vehicle->license_plate }}</td>
                <td>{{ $maintenance->maintenanceService->name ?? '-' }}</td>
                <td>{{ $maintenance->partner->name ?? '-' }}</td>
                <td class="text-right cost">{{ number_format($maintenance->cost, 0, ',', '.') }}</td>
                <td class="text-center">{{ $maintenance->mileage ? number_format($maintenance->mileage, 0, ',', '.') : '-' }}</td>
                <td>{{ $maintenance->user->name }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">Không có dữ liệu</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Hệ thống quản lý Bình An - {{ now()->format('Y') }}
    </div>
</body>
</html>
