@extends('emails.layouts.base')

@section('content')
    <h2>
        @if($alertType === 'critical')
            🚨 Critical Stock Alert
        @elseif($alertType === 'out_of_stock')
            ❌ Out of Stock Alert
        @else
            ⚠️ Low Stock Alert
        @endif
    </h2>
    
    <p>Dear Administrator,</p>
    
    @if($alertType === 'critical')
        <div class="alert-box">
            <p><strong>Critical Alert:</strong> Several medicines have reached critically low stock levels and require immediate attention.</p>
        </div>
    @elseif($alertType === 'out_of_stock')
        <div class="alert-box">
            <p><strong>Urgent:</strong> Some medicines are completely out of stock and need immediate restocking.</p>
        </div>
    @else
        <div class="info-box">
            <p><strong>Notice:</strong> Some medicines are running low and may need to be restocked soon.</p>
        </div>
    @endif
    
    <p>Here's a summary of the current stock situation:</p>
    
    @if(count($criticalStockMedicines) > 0)
    <div class="alert-box">
        <h3>🚨 Critical Stock (Less than 10 units)</h3>
        <table class="details-table">
            <thead>
                <tr>
                    <th>Medicine Name</th>
                    <th>Current Stock</th>
                    <th>Reorder Level</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($criticalStockMedicines as $medicine)
                <tr>
                    <td><strong>{{ $medicine['medicine_name'] }}</strong></td>
                    <td style="color: #f56565;"><strong>{{ $medicine['current_stock'] }}</strong></td>
                    <td>{{ $medicine['reorder_level'] ?? '20' }}</td>
                    <td>
                        @if($medicine['current_stock'] == 0)
                            <span style="color: #f56565;">OUT OF STOCK</span>
                        @else
                            <span style="color: #f56565;">CRITICAL</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    
    @if(count($lowStockMedicines) > 0 && count($criticalStockMedicines) > 0)
    <div class="divider"></div>
    @endif
    
    @if(count($lowStockMedicines) > 0)
    <div class="info-box">
        <h3>⚠️ Low Stock (10-20 units)</h3>
        <table class="details-table">
            <thead>
                <tr>
                    <th>Medicine Name</th>
                    <th>Current Stock</th>
                    <th>Reorder Level</th>
                    <th>Days Until Reorder</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lowStockMedicines as $medicine)
                <tr>
                    <td><strong>{{ $medicine['medicine_name'] }}</strong></td>
                    <td style="color: #f6ad55;"><strong>{{ $medicine['current_stock'] }}</strong></td>
                    <td>{{ $medicine['reorder_level'] ?? '20' }}</td>
                    <td>{{ $medicine['days_until_reorder'] ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    
    <div class="alert-box">
        <h3>⚡ Immediate Action Required</h3>
        <ol style="margin: 15px 0; padding-left: 20px;">
            @if(count($criticalStockMedicines) > 0)
            <li><strong>Critical Stock:</strong> Order {{ count($criticalStockMedicines) }} medicine(s) immediately</li>
            @endif
            @if(count($lowStockMedicines) > 0)
            <li><strong>Low Stock:</strong> Plan orders for {{ count($lowStockMedicines) }} medicine(s)</li>
            @endif
            <li>Review supplier availability and lead times</li>
            <li>Consider emergency procurement if necessary</li>
            <li>Update inventory management system</li>
            <li>Notify medical staff of potential shortages</li>
        </ol>
    </div>
    
    <div class="success-box">
        <h3>📈 Stock Management Tips</h3>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li>Set up automatic reorder alerts for better inventory management</li>
            <li>Maintain relationships with multiple suppliers for reliability</li>
            <li>Track usage patterns to predict future needs</li>
            <li>Keep emergency stock for critical medicines</li>
            <li>Review and update reorder levels regularly</li>
        </ul>
    </div>
    
    <div class="info-box">
        <h3>📊 Summary</h3>
        <table class="details-table">
            <tr>
                <th>Total Items Requiring Attention</th>
                <td><strong>{{ $totalItems }}</strong></td>
            </tr>
            <tr>
                <th>Critical Stock Items</th>
                <td style="color: #f56565;"><strong>{{ count($criticalStockMedicines) }}</strong></td>
            </tr>
            <tr>
                <th>Low Stock Items</th>
                <td style="color: #f6ad55;"><strong>{{ count($lowStockMedicines) }}</strong></td>
            </tr>
            <tr>
                <th>Alert Generated</th>
                <td>{{ now()->format('F j, Y g:i A') }}</td>
            </tr>
        </table>
    </div>
    
    <p>Please take immediate action to ensure continuous availability of medicines for patient care. Timely restocking is crucial for maintaining quality healthcare services.</p>
    
    <div style="margin-top: 30px;">
        <p><strong>Best regards,</strong><br>
        BOKOD CMS Inventory System</p>
    </div>
@endsection