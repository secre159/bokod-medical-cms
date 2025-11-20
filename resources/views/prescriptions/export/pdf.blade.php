<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>My Prescriptions PDF</title>
    <style>
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; color: #333; }
        h1 { font-size: 18px; margin: 0 0 10px; }
        h2 { font-size: 14px; margin: 0 0 8px; }
        .meta { margin-bottom: 12px; }
        .meta div { margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; }
        th { background: #f5f5f5; text-align: left; }
        .small { font-size: 11px; color: #666; }
    </style>
</head>
<body>
    <h1>Prescription Summary</h1>
    <div class="meta">
        <div><strong>Patient:</strong> {{ $patient->patient_name ?? 'N/A' }}</div>
        <div class="small">
            Generated at {{ $generatedAt }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Medicine</th>
                <th>Generic</th>
                <th>Dosage</th>
                <th>Frequency</th>
                <th>Qty</th>
                <th>Status</th>
                <th>Appt Date</th>
                <th>Reason</th>
            </tr>
        </thead>
        <tbody>
            @forelse($prescriptions as $p)
                <tr>
                    <td>{{ $p->prescribed_date ? $p->prescribed_date->format('Y-m-d') : ($p->created_at?->format('Y-m-d') ?? 'N/A') }}</td>
                    <td>{{ $p->medicine->medicine_name ?? $p->medicine_name ?? 'N/A' }}</td>
                    <td>{{ $p->medicine->generic_name ?? $p->generic_name ?? 'N/A' }}</td>
                    <td>{{ $p->dosage ?? 'N/A' }}</td>
                    <td>{{ $p->frequency ?? 'N/A' }}</td>
                    <td>{{ $p->quantity ?? 'N/A' }}</td>
                    <td>{{ ucfirst($p->status ?? 'N/A') }}</td>
                    <td>{{ $p->appointment_date ? $p->appointment_date->format('Y-m-d') : 'N/A' }}</td>
                    <td>{{ $p->appointment_reason ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">No prescriptions found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
