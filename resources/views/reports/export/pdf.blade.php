<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ucfirst($type) }} Report - Bokod CMS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #0f5132;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #0f5132;
            margin: 0;
            font-size: 24px;
        }
        
        .header .subtitle {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .report-info {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .report-info div {
            display: table-cell;
            width: 33.33%;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }
        
        .report-info div strong {
            color: #0f5132;
        }
        
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        
        .stats-grid .stat-item {
            display: table-cell;
            width: 25%;
            padding: 15px;
            text-align: center;
            background-color: #e9ecef;
            border: 1px solid #dee2e6;
        }
        
        .stats-grid .stat-number {
            font-size: 18px;
            font-weight: bold;
            color: #0f5132;
        }
        
        .stats-grid .stat-label {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .data-table th,
        .data-table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
        }
        
        .data-table th {
            background-color: #0f5132;
            color: white;
            font-weight: bold;
            font-size: 11px;
        }
        
        .data-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .footer {
            position: fixed;
            bottom: 20px;
            right: 20px;
            font-size: 10px;
            color: #666;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    @php
        $fmt = function ($v, $d = 2) {
            return is_numeric($v) ? number_format((float)$v, $d) : number_format(0, $d);
        };
    @endphp
    <div class="header">
        <h1>{{ ucfirst($type) }} Report</h1>
        <div class="subtitle">Bokod Medical CMS - Generated on {{ now()->format('F d, Y h:i A') }}</div>
    </div>
    
    <div class="report-info">
        <div>
            <strong>Report Type:</strong><br>
            {{ ucfirst($type) }} Report
        </div>
        <div>
            <strong>Date Range:</strong><br>
            {{ Carbon\Carbon::parse($dateFrom)->format('M d, Y') }} - {{ Carbon\Carbon::parse($dateTo)->format('M d, Y') }}
        </div>
        <div>
            <strong>Total Records:</strong><br>
            {{ is_countable($data) ? count($data) : 0 }} items
        </div>
    </div>
    
    <!-- Statistics Summary -->
    @if(isset($stats) && is_array($stats))
    <div class="stats-grid">
        @if($type == 'patients' && isset($stats['patients']))
        <div class="stat-item">
            <div class="stat-number">{{ $stats['patients']['total'] ?? 0 }}</div>
            <div class="stat-label">Total Patients</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $stats['patients']['new_this_month'] ?? 0 }}</div>
            <div class="stat-label">New This Month</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $stats['patients']['active'] ?? 0 }}</div>
            <div class="stat-label">Active Patients</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ ($stats['patients']['by_gender']['male'] ?? 0) + ($stats['patients']['by_gender']['female'] ?? 0) }}</div>
            <div class="stat-label">Gender Records</div>
        </div>
        @elseif($type == 'prescriptions' && isset($stats['prescriptions']))
        <div class="stat-item">
            <div class="stat-number">{{ $stats['prescriptions']['total'] ?? 0 }}</div>
            <div class="stat-label">Total Prescriptions</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $stats['prescriptions']['dispensed'] ?? 0 }}</div>
            <div class="stat-label">Dispensed</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $stats['prescriptions']['active'] ?? 0 }}</div>
            <div class="stat-label">Active</div>
        </div>
        <div class="stat-item">
<div class="stat-number">₱{{ $fmt($stats['financial']['revenue_month'] ?? null) }}</div>
            <div class="stat-label">Revenue</div>
        </div>
        @elseif($type == 'medicines' && isset($stats['medicines']))
        <div class="stat-item">
            <div class="stat-number">{{ $stats['medicines']['total'] ?? 0 }}</div>
            <div class="stat-label">Total Medicines</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $stats['medicines']['active'] ?? 0 }}</div>
            <div class="stat-label">Active</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $stats['medicines']['low_stock'] ?? 0 }}</div>
            <div class="stat-label">Low Stock</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ $stats['medicines']['out_of_stock'] ?? 0 }}</div>
            <div class="stat-label">Out of Stock</div>
        </div>
        @elseif($type == 'financial' && isset($stats['financial']))
        <div class="stat-item">
            <div class="stat-number">₱{{ number_format($stats['financial']['revenue_month'] ?? 0, 2) }}</div>
            <div class="stat-label">Monthly Revenue</div>
        </div>
        <div class="stat-item">
<div class="stat-number">₱{{ $fmt($stats['financial']['revenue_today'] ?? null) }}</div>
            <div class="stat-label">Today's Revenue</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ count($data) }}</div>
            <div class="stat-label">Transactions</div>
        </div>
        <div class="stat-item">
<div class="stat-number">₱{{ $fmt(collect($data)->sum('total_amount')) }}</div>
            <div class="stat-label">Total Value</div>
        </div>
        @endif
    </div>
    @endif
    
    <!-- Data Table -->
    @if(is_countable($data) && count($data) > 0)
    <table class="data-table">
        <thead>
            <tr>
                @if($type == 'patients')
                <th>Patient Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Registration Date</th>
                <th>Prescriptions</th>
                @elseif($type == 'prescriptions')
                <th>Date</th>
                <th>Patient</th>
                <th>Medicine</th>
                <th>Dosage</th>
                <th>Quantity</th>
                <th>Status</th>
                <th>Total Value</th>
                @elseif($type == 'medicines')
                <th>Medicine Name</th>
                <th>Category</th>
                <th>Stock Quantity</th>
                <th>Unit Price</th>
                <th>Prescriptions</th>
                <th>Status</th>
                @elseif($type == 'financial')
                <th>Date</th>
                <th>Patient</th>
                <th>Medicine</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total Amount</th>
                <th>Status</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr>
                @if($type == 'patients')
                <td>{{ $row['patient_name'] ?? 'N/A' }}</td>
                <td>{{ $row['email'] ?? 'N/A' }}</td>
                <td>{{ $row['phone'] ?? 'N/A' }}</td>
                <td>{{ $row['age'] ?? 'N/A' }}</td>
                <td>{{ ucfirst($row['gender'] ?? 'N/A') }}</td>
                <td>{{ $row['created_at'] ? Carbon\Carbon::parse($row['created_at'])->format('M d, Y') : 'N/A' }}</td>
                <td>{{ $row['prescriptions_count'] ?? 0 }}</td>
                @elseif($type == 'prescriptions')
                <td>{{ $row['prescribed_date'] ? Carbon\Carbon::parse($row['prescribed_date'])->format('M d, Y') : 'N/A' }}</td>
                <td>{{ $row['patient_name'] ?? 'N/A' }}</td>
                <td>{{ $row['medicine_name'] ?? 'N/A' }}</td>
                <td>{{ $row['dosage'] ?? 'N/A' }}</td>
                <td>{{ $row['quantity'] ?? 'N/A' }}</td>
                <td>{{ ucfirst($row['status'] ?? 'N/A') }}</td>
<td>₱{{ $fmt($row['total_value'] ?? null) }}</td>
                @elseif($type == 'medicines')
                <td>{{ $row['medicine_name'] ?? 'N/A' }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $row['category'] ?? 'N/A')) }}</td>
                <td>{{ $row['stock_quantity'] ?? 0 }}</td>
<td>₱{{ $fmt($row['unit_price'] ?? null) }}</td>
                <td>{{ $row['prescriptions_count'] ?? 0 }}</td>
                <td>{{ ucfirst($row['status'] ?? 'N/A') }}</td>
                @elseif($type == 'financial')
                <td>{{ $row['date'] ? Carbon\Carbon::parse($row['date'])->format('M d, Y') : 'N/A' }}</td>
                <td>{{ $row['patient_name'] ?? 'N/A' }}</td>
                <td>{{ $row['medicine_name'] ?? 'N/A' }}</td>
                <td>{{ $row['quantity'] ?? 'N/A' }}</td>
<td>₱{{ $fmt($row['unit_price'] ?? null) }}</td>
<td>₱{{ $fmt($row['total_amount'] ?? null) }}</td>
                <td>{{ ucfirst($row['status'] ?? 'N/A') }}</td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">
        <strong>No data available</strong><br>
        No {{ $type }} data found for the selected date range.
    </div>
    @endif
    
    <div class="footer">
        Generated by Bokod Medical CMS | Page <script type="text/php">
            if (isset($pdf)) {
                $pdf->page_script('$pdf->text(530, 800, "Page $PAGE_NUM of $PAGE_COUNT", "Arial", 8);');
            }
        </script>
    </div>
</body>
</html>