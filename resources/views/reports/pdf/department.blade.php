<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Báo cáo Chuyển viện Bình An</title>
    <style>
        @page { 
            size: A4 landscape;
            margin: 15mm;
        }
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 11px; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 15px; 
        }
        th, td { 
            border: 1px solid #333; 
            padding: 6px 4px; 
            text-align: left;
            vertical-align: top;
        }
        th { 
            background-color: #e8e8e8; 
            font-weight: bold; 
            text-align: center;
        }
        .header { 
            text-align: center; 
            margin-bottom: 15px; 
        }
        .header h2 {
            margin: 5px 0;
            font-size: 16px;
        }
        .header p {
            margin: 3px 0;
            font-size: 12px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .col-stt { width: 5%; text-align: center; }
        .col-date { width: 8%; }
        .col-patient { width: 15%; }
        .col-from { width: 12%; }
        .col-to { width: 12%; }
        .col-driver { width: 11%; }
        .col-medical { width: 11%; }
        .col-note { width: 13%; }
        .col-commission { width: 7%; text-align: right; }
        .col-partner { width: 10%; }
        .signature-section {
            margin-top: 40px;
            text-align: right;
        }
        .signature-box {
            display: inline-block;
            min-width: 250px;
            text-align: center;
        }
        .signature-date {
            font-size: 12px;
            margin-bottom: 5px;
        }
        .signature-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .signature-name {
            font-size: 12px;
            font-weight: bold;
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>BÁO CÁO CHUYỂN VIỆN BÌNH AN</h2>
        <p>Thông tin tháng báo cáo: Tháng {{ \Carbon\Carbon::parse($dateFrom)->format('m') }} Năm {{ \Carbon\Carbon::parse($dateFrom)->format('Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                @if(in_array('col-stt', $visibleColumns ?? []))
                <th class="col-stt">Số thứ tự</th>
                @endif
                @if(in_array('col-date', $visibleColumns ?? []))
                <th class="col-date">Ngày</th>
                @endif
                @if(in_array('col-patient', $visibleColumns ?? []))
                <th class="col-patient">Họ tên người bệnh</th>
                @endif
                @if(in_array('col-from', $visibleColumns ?? []))
                <th class="col-from">Nơi đi</th>
                @endif
                @if(in_array('col-to', $visibleColumns ?? []))
                <th class="col-to">Nơi đến</th>
                @endif
                @if(in_array('col-driver', $visibleColumns ?? []))
                <th class="col-driver">Lái xe</th>
                @endif
                @if(in_array('col-staff', $visibleColumns ?? []))
                <th class="col-medical">Nhân viên Y tế</th>
                @endif
                @if(in_array('col-note', $visibleColumns ?? []))
                <th class="col-note">Ghi chú</th>
                @endif
                @if(in_array('col-commission', $visibleColumns ?? []))
                <th class="col-commission">Hoa hồng</th>
                @endif
                @if(in_array('col-partner', $visibleColumns ?? []))
                <th class="col-partner">Nơi nhận hoa hồng</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($incidents as $index => $incident)
            <tr>
                @if(in_array('col-stt', $visibleColumns ?? []))
                <td class="text-center">{{ $index + 1 }}</td>
                @endif
                @if(in_array('col-date', $visibleColumns ?? []))
                <td>{{ $incident->date->format('d/m/Y') }}</td>
                @endif
                @if(in_array('col-patient', $visibleColumns ?? []))
                <td>{{ $incident->patient ? $incident->patient->name : '-' }}</td>
                @endif
                @if(in_array('col-from', $visibleColumns ?? []))
                <td>{{ $incident->fromLocation ? $incident->fromLocation->name : '-' }}</td>
                @endif
                @if(in_array('col-to', $visibleColumns ?? []))
                <td>{{ $incident->toLocation ? $incident->toLocation->name : '-' }}</td>
                @endif
                @if(in_array('col-driver', $visibleColumns ?? []))
                <td>{{ $incident->drivers->pluck('name')->join(', ') ?: '-' }}</td>
                @endif
                @if(in_array('col-staff', $visibleColumns ?? []))
                <td>{{ $incident->medicalStaff->pluck('name')->join(', ') ?: '-' }}</td>
                @endif
                @if(in_array('col-note', $visibleColumns ?? []))
                <td>{{ $incident->custom_note ?? $incident->summary ?? '-' }}</td>
                @endif
                @if(in_array('col-commission', $visibleColumns ?? []))
                <td class="text-right">{{ $incident->commission_amount ? number_format($incident->commission_amount, 0, ',', '.') : '-' }}</td>
                @endif
                @if(in_array('col-partner', $visibleColumns ?? []))
                <td>{{ $incident->partner ? $incident->partner->name : '-' }}</td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-date">Lâm Đồng, ngày {{ now()->format('d') }} tháng {{ now()->format('m') }} năm {{ now()->format('Y') }}</div>
            <div class="signature-title">GIÁM ĐỐC</div>
            <div class="signature-name">NGÔ QUYỀN</div>
        </div>
    </div>
</body>
</html>
