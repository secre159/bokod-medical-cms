@extends('adminlte::page')

@section('title', 'Medicine Inventory | Bokod CMS')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">Medicine Inventory Management</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                <li class="breadcrumb-item active">Medicines</li>
            </ol>
        </div>
    </div>
@endsection

@section('adminlte_css_pre')
<meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('content')
<div id="medicineContent">
    <!-- Success/Error Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="icon fas fa-check"></i> {{ session('success') }}
        </div>
    @endif

    @if ($errors->has('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="icon fas fa-ban"></i> {{ $errors->first('error') }}
        </div>
    @endif

    <!-- Statistics Row -->
    <div class="row mb-3">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['total'] }}</h3>
                    <p>Total Medicines</p>
                </div>
                <div class="icon">
                    <i class="fas fa-pills"></i>
                </div>
                <a href="{{ route('medicines.index') }}" class="small-box-footer">
                    View All <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['low_stock'] }}</h3>
                    <p>Low Stock Alerts</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <a href="?stock_filter=low_stock" class="small-box-footer">
                    Check Stock <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['expired'] }}</h3>
                    <p>Expired Medicines</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <a href="?stock_filter=expired" class="small-box-footer">
                    View Expired <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['expiring_soon'] }}</h3>
                    <p>Expiring Soon</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <a href="?stock_filter=expiring_soon" class="small-box-footer">
                    Check Expiry <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card {{ (request('status') || request('category') || request('stock_filter') || request('search')) ? '' : 'collapsed-card' }}" id="filtersCard">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-filter mr-2"></i>Filters & Search</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-card-widget="collapse" id="filterToggle" title="Toggle Filters">
                    <i class="fas {{ (request('status') || request('category') || request('stock_filter') || request('search')) ? 'fa-minus' : 'fa-plus' }}" id="toggleIcon"></i>
                    <span class="ml-1" id="toggleText">{{ (request('status') || request('category') || request('stock_filter') || request('search')) ? 'Hide' : 'Show' }}</span>
                </button>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('medicines.index') }}" id="filterForm">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control" data-original-name="status">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                                <option value="discontinued" {{ request('status') == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="category">Category</label>
                            <select name="category" id="category" class="form-control" data-original-name="category">
                                <option value="">All Categories</option>
                                @foreach($categories as $key => $category)
                                    <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="stock_filter">Stock Status</label>
                            <select name="stock_filter" id="stock_filter" class="form-control" data-original-name="stock_filter">
                                <option value="">All Stock</option>
                                <option value="low_stock" {{ request('stock_filter') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                                <option value="out_of_stock" {{ request('stock_filter') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                                <option value="expired" {{ request('stock_filter') == 'expired' ? 'selected' : '' }}>Expired</option>
                                <option value="expiring_soon" {{ request('stock_filter') == 'expiring_soon' ? 'selected' : '' }}>Expiring Soon</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="search">Search</label>
                            <div class="input-group">
                                <input type="text" name="search" id="search" class="form-control" 
                                       placeholder="Medicine name, generic, brand, manufacturer, or supplier..." 
                                       value="{{ request('search') }}" data-original-name="search">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter mr-2"></i>Apply Filters
                        </button>
                        <a href="{{ route('medicines.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times mr-2"></i>Clear Filters
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Main Medicines Card -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-pills mr-2"></i>Medicines List
            </h3>
            <div class="card-tools">
                <a href="{{ route('medicines.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus mr-1"></i>Add New Medicine
                </a>
                <a href="{{ route('medicines.stock') }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-boxes mr-1"></i>Stock Management
                </a>
            </div>
        </div>
        <div class="card-body table-responsive">
            @if($medicines->count() > 0)
                <table class="table table-bordered table-striped" id="medicinesTable">
                    <thead>
                        <tr style="background-color: #f8f9fa;">
                            <th width="8%" class="text-center">Article</th>
                            <th width="25%">Description</th>
                            <th width="10%" class="text-center">Batch Number</th>
                            <th width="10%" class="text-center">Stock Number</th>
                            <th width="10%" class="text-center">Unit Measure</th>
                            <th width="12%" class="text-center">Balance (Quantity)</th>
                            <th width="10%" class="text-center">On Hand Per Count</th>
                            <th width="10%" class="text-center">Shortage/Overage</th>
                            <th width="12%">Remarks</th>
                            <th width="13%" class="text-center">Expiry Status</th>
                            <th width="10%" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($medicines as $medicine)
                        <tr>
                            <td class="text-center">{{ $medicine->id }}</td>
                            <td>
                                <strong>{{ $medicine->medicine_name }}</strong>
                                @if($medicine->brand_name)
                                    <br><small class="text-muted">{{ $medicine->brand_name }}</small>
                                @endif
                                @if($medicine->strength)
                                    <br><small class="text-info">{{ $medicine->strength }}</small>
                                @endif
                                @php
                                    $batchCount = \App\Models\Medicine::where('medicine_name', $medicine->medicine_name)->count();
                                @endphp
                                @if($batchCount > 1)
                                    <br><small class="badge badge-info"><i class="fas fa-layer-group"></i> {{ $batchCount }} batches</small>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge badge-primary">{{ $medicine->batch_number }}</span>
                                @if($medicine->manufacturing_date)
                                    <br><small class="text-muted">Mfg: {{ $medicine->manufacturing_date->format('M Y') }}</small>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge badge-secondary">MED-{{ str_pad($medicine->id, 4, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="text-center">{{ $medicine->unit_measure ?? 'pcs' }}</td>
                            <td class="text-center">
                                <strong class="{{ $medicine->stock_quantity <= $medicine->minimum_stock ? 'text-danger' : 'text-success' }}">
                                    {{ $medicine->stock_quantity }}
                                </strong>
                                @if($medicine->minimum_stock)
                                    <br><small class="text-muted">Min: {{ $medicine->minimum_stock }}</small>
                                @endif
                            </td>
                            <td class="text-center">
                                <input type="number" class="form-control form-control-sm text-center" 
                                       style="width: 80px; margin: 0 auto;" 
                                       placeholder="{{ $medicine->stock_quantity }}" 
                                       title="Physical count"
                                       data-medicine-id="{{ $medicine->id }}"
                                       data-original-value="{{ $medicine->on_hand_per_count ?? 0 }}"
                                       value="{{ $medicine->on_hand_per_count ?? '' }}">
                            </td>
                            <td class="text-center">
                                <div class="shortage-display" data-balance="{{ $medicine->balance_per_card ?? $medicine->stock_quantity }}">
                                    @if($medicine->shortage_overage > 0)
                                        <span class="text-success">+{{ $medicine->shortage_overage }}</span><br><small class="text-muted">Overage</small>
                                    @elseif($medicine->shortage_overage < 0)
                                        <span class="text-danger">{{ $medicine->shortage_overage }}</span><br><small class="text-muted">Shortage</small>
                                    @else
                                        <small class="text-muted">-</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div style="min-height: 40px; font-size: 0.8rem;">
                                    @if($medicine->inventory_remarks)
                                        <span class="text-dark">{{ Str::limit($medicine->inventory_remarks, 50) }}</span>
                                    @else
                                        <small class="text-muted">No remarks</small>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                <div style="min-height: 40px;">
                                    @if($medicine->status == 'expired' || ($medicine->expiry_date && $medicine->expiry_date->isPast()))
                                        <span class="badge badge-danger">Expired</span>
                                        @if($medicine->expiry_date)
                                            <br><small class="text-muted">{{ $medicine->expiry_date->format('M d, Y') }}</small>
                                        @endif
                                    @elseif($medicine->expiry_date && now()->startOfDay()->diffInDays($medicine->expiry_date->startOfDay(), false) <= 30 && $medicine->expiry_date->isFuture())
                                        <span class="badge badge-warning">Expiring Soon</span>
                                        <br><small class="text-muted">{{ $medicine->expiry_date->format('M d, Y') }}</small>
                                        <br><small class="text-warning">{{ now()->startOfDay()->diffInDays($medicine->expiry_date->startOfDay(), false) }} days left</small>
                                    @elseif($medicine->expiry_date && now()->startOfDay()->diffInDays($medicine->expiry_date->startOfDay(), false) <= 90 && $medicine->expiry_date->isFuture())
                                        <span class="badge badge-info">Expiring in 3 months</span>
                                        <br><small class="text-muted">{{ $medicine->expiry_date->format('M d, Y') }}</small>
                                        <br><small class="text-info">{{ now()->startOfDay()->diffInDays($medicine->expiry_date->startOfDay(), false) }} days left</small>
                                    @elseif($medicine->expiry_date)
                                        <span class="badge badge-success">Good</span>
                                        <br><small class="text-muted">Exp: {{ $medicine->expiry_date->format('M d, Y') }}</small>
                                        <br><small class="text-success">{{ now()->startOfDay()->diffInDays($medicine->expiry_date->startOfDay(), false) }} days left</small>
                                    @else
                                        <span class="badge badge-secondary">No Date</span>
                                        <br><small class="text-muted">Not specified</small>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" style="flex-wrap: wrap; gap: 2px;">
                                    <button type="button" class="btn btn-outline-primary btn-xs" 
                                            onclick="location.href='{{ route('medicines.edit', $medicine) }}'" 
                                            title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-info btn-xs" 
                                            onclick="location.href='{{ route('medicines.show', $medicine) }}'" 
                                            title="View">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($medicine->shortage_overage != 0)
                                        <button type="button" class="btn btn-outline-warning btn-xs btn-adjust-stock" 
                                                data-medicine-id="{{ $medicine->id }}"
                                                data-medicine-name="{{ $medicine->medicine_name }}"
                                                data-shortage-overage="{{ $medicine->shortage_overage }}"
                                                title="Adjust Stock">
                                            <i class="fas fa-balance-scale"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Showing {{ $medicines->firstItem() }} to {{ $medicines->lastItem() }} of {{ $medicines->total() }} results
                    </div>
                    {{ $medicines->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-pills fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No medicines found</h5>
                    <p class="text-muted">Try adjusting your search criteria or add a new medicine.</p>
                    <a href="{{ route('medicines.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i>Add First Medicine
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Stock Update Modal -->
    <div class="modal fade" id="stockUpdateModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Update Stock</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="stockUpdateForm">
                        <input type="hidden" id="medicineId">
                        <div class="form-group">
                            <label>Medicine:</label>
                            <p class="form-control-static" id="medicineName"></p>
                        </div>
                        <div class="form-group">
                            <label>Current Stock:</label>
                            <p class="form-control-static">
                                <strong id="currentStock"></strong> <span id="stockUnit"></span>
                            </p>
                        </div>
                        <div class="form-group">
                            <label for="operation">Operation:</label>
                            <select class="form-control" id="operation" required>
                                <option value="add">Add Stock</option>
                                <option value="subtract">Subtract Stock</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="quantity">Quantity:</label>
                            <input type="number" class="form-control" id="quantity" min="1" required>
                        </div>
                        <div class="form-group">
                            <label for="notes">Notes (optional):</label>
                            <textarea class="form-control" id="notes" rows="2" placeholder="Reason for stock update..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="updateStockBtn">Update Stock</button>
                </div>
            </div>
        </div>
    </div>
</div><!-- End medicineContent -->

@endsection

@section('css')
<style>
    .small-box h3 {
        font-size: 2.2rem;
        font-weight: bold;
        margin: 0 0 10px 0;
        white-space: nowrap;
        padding: 0;
    }
    
    .table th {
        font-weight: 600;
        background-color: #f8f9fa;
    }
    
    .dropdown-menu {
        min-width: 160px;
    }
    
    .badge {
        font-size: 0.75em;
    }
    
    /* Collapsed card functionality */
    .card.collapsed-card .card-body {
        display: none;
    }
    
    /* Filter toggle button styling */
    #filterToggle {
        font-size: 0.875rem;
        white-space: nowrap;
    }
    
    #filterToggle:focus {
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }
    
    #filtersCard {
        transition: all 0.3s ease;
        margin-bottom: 1rem;
    }
    
    /* Inventory table styling */
    #medicinesTable {
        font-size: 0.9rem;
    }
    
    #medicinesTable th {
        border: 1px solid #dee2e6;
        padding: 8px 4px;
        vertical-align: middle;
        font-weight: 600;
        background-color: #f8f9fa;
    }
    
    #medicinesTable td {
        border: 1px solid #dee2e6;
        padding: 8px 4px;
        vertical-align: middle;
    }
    
    .btn-xs {
        padding: 2px 6px;
        font-size: 11px;
        line-height: 1.2;
    }
    
    .form-control-sm.text-center {
        font-size: 0.8rem;
        padding: 2px 4px;
    }
    
    .shortage-display {
        min-height: 30px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    
    .table-active {
        background-color: #f8f9fa;
    }
    
    .badge {
        font-size: 0.7rem;
    }
    
    /* Expiry status styling */
    .badge-danger {
        background-color: #dc3545;
    }
    
    .badge-warning {
        background-color: #ffc107;
        color: #212529;
    }
    
    .badge-info {
        background-color: #17a2b8;
    }
    
    .badge-success {
        background-color: #28a745;
    }
    
    .badge-secondary {
        background-color: #6c757d;
    }
    
    /* Print styles for inventory */
    @media print {
        .card-tools, .btn-group, .filter-section {
            display: none !important;
        }
        
        #medicinesTable {
            font-size: 0.8rem;
        }
        
        .shortage-display input {
            border: 1px solid #000;
        }
    }</style>
</style>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Show alert function
    function showAlert(message, type) {
        var alertClass = 'alert-' + type;
        var iconClass = type === 'success' ? 'fa-check' : 'fa-exclamation-triangle';
        
        var alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
            '<i class="fas ' + iconClass + ' mr-2"></i>' + message +
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                '<span aria-hidden="true">&times;</span>' +
            '</button>' +
        '</div>';
        
        // Remove existing alerts
        $('.alert').remove();
        
        // Add new alert at the top of content
        $('#medicineContent').prepend(alertHtml);
        
        // Auto-hide success messages after 5 seconds
        if (type === 'success') {
            setTimeout(function() {
                $('.alert-success').fadeOut();
            }, 5000);
        }
    }
    
    // Make showAlert globally available
    window.showAlert = showAlert;
    
    // Auto-submit form on filter change
    $('#status, #category, #stock_filter').change(function() {
        // Remove empty parameters before submitting
        $('#filterForm input, #filterForm select').each(function() {
            if ($(this).val() === '' || $(this).val() === null) {
                $(this).removeAttr('name');
            } else {
                // Restore name attribute if it was removed
                var originalName = $(this).data('original-name');
                if (originalName && !$(this).attr('name')) {
                    $(this).attr('name', originalName);
                }
            }
        });
        $('#filterForm').submit();
    });
    
    // Calculate shortage/overage when physical count changes
    $(document).on('input', '.form-control-sm[type="number"]', function() {
        const physicalCount = parseFloat($(this).val()) || 0;
        const balance = parseFloat($(this).closest('tr').find('.shortage-display').data('balance')) || 0;
        const shortageDisplay = $(this).closest('tr').find('.shortage-display');
        
        const difference = physicalCount - balance;
        
        if (difference === 0 || physicalCount === 0) {
            shortageDisplay.html('<small class="text-muted">-</small>');
        } else if (difference > 0) {
            shortageDisplay.html(`<span class="text-success">+${difference}</span><br><small class="text-muted">Overage</small>`);
        } else {
            shortageDisplay.html(`<span class="text-danger">${difference}</span><br><small class="text-muted">Shortage</small>`);
        }
    });
    
    // Save physical count to backend on blur/enter
    $(document).on('blur keypress', '.form-control-sm[type="number"]', function(e) {
        if (e.type === 'keypress' && e.which !== 13) return; // Only handle Enter key for keypress
        
        const $input = $(this);
        const medicineId = $input.data('medicine-id');
        const physicalCount = parseInt($input.val()) || 0;
        const originalValue = parseInt($input.data('original-value')) || 0;
        
        // Only save if value has changed and medicine ID exists
        if (physicalCount !== originalValue && medicineId) {
            savePhysicalCount(medicineId, physicalCount, $input);
        }
    });
    
    // Handle stock adjustment buttons
    $(document).on('click', '.btn-adjust-stock', function() {
        const medicineId = $(this).data('medicine-id');
        const medicineName = $(this).data('medicine-name');
        const shortageOverage = parseInt($(this).data('shortage-overage')) || 0;
        
        if (shortageOverage === 0) {
            showAlert('No adjustment needed. Stock is balanced.', 'info');
            return;
        }
        
        const adjustmentType = shortageOverage > 0 ? 'increase' : 'decrease';
        const adjustmentAmount = Math.abs(shortageOverage);
        
        if (confirm(`Adjust stock for ${medicineName}?\n\nThis will ${adjustmentType} the stock by ${adjustmentAmount} units to match the physical count.`)) {
            const reason = prompt('Please provide a reason for this adjustment:');
            if (reason && reason.trim()) {
                adjustStockFromCount(medicineId, reason.trim());
            }
        }
    });

    // Simple table styling instead of DataTable for cleaner look
    @if($medicines->count() > 0)
    // Add hover effects
    $('#medicinesTable tbody tr').hover(
        function() { $(this).addClass('table-active'); },
        function() { $(this).removeClass('table-active'); }
    );
    @endif
    
    // Handle filter card toggle behavior
    const hasActiveFilters = {{ (request('status') || request('category') || request('stock_filter') || request('search')) ? 'true' : 'false' }};
    
    if (hasActiveFilters) {
        console.log('Active filters detected, keeping card expanded');
        $('#filtersCard').removeClass('collapsed-card');
        $('#toggleIcon').removeClass('fa-plus').addClass('fa-minus');
        $('#toggleText').text('Hide');
    }
    
    // Manual filter card toggle handler
    $('#filterToggle').on('click', function() {
        console.log('Filter toggle clicked');
        const card = $('#filtersCard');
        const icon = $('#toggleIcon');
        const text = $('#toggleText');
        
        setTimeout(function() {
            if (card.hasClass('collapsed-card')) {
                icon.removeClass('fa-minus').addClass('fa-plus');
                text.text('Show');
                console.log('Card collapsed');
            } else {
                icon.removeClass('fa-plus').addClass('fa-minus');
                text.text('Hide');
                console.log('Card expanded');
            }
        }, 100);
    });

    // Stock update modal
    $('.stock-update').click(function(e) {
        e.preventDefault();
        
        var medicineId = $(this).data('id');
        var medicineName = $(this).data('name');
        var currentStock = $(this).data('current');
        var unit = $(this).data('unit');
        
        $('#medicineId').val(medicineId);
        $('#medicineName').text(medicineName);
        $('#currentStock').text(currentStock);
        $('#stockUnit').text(unit);
        $('#quantity').val('');
        $('#notes').val('');
        $('#operation').val('add');
        
        $('#stockUpdateModal').modal('show');
    });

    // Update stock button
    $('#updateStockBtn').click(function() {
        var medicineId = $('#medicineId').val();
        var operation = $('#operation').val();
        var quantity = $('#quantity').val();
        var notes = $('#notes').val();

        if (!quantity || quantity < 1) {
            showAlert('Please enter a valid quantity', 'danger');
            return;
        }
        
        // Disable the button and show loading state
        var $btn = $(this);
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Updating...');

        $.ajax({
            url: '/medicines/' + medicineId + '/update-stock',
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                operation: operation,
                quantity: quantity,
                notes: notes
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    showAlert(response.message, 'success');
                    $('#stockUpdateModal').modal('hide');
                    
                    // Refresh the page to show updated data
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                } else {
                    showAlert('Error: ' + response.message, 'danger');
                }
            },
            error: function(xhr) {
                var errorMessage = 'Error updating stock. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showAlert(errorMessage, 'danger');
            },
            complete: function() {
                // Re-enable the button
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
    
    // Handle discontinue medicine forms with AJAX
    $(document).on('submit', 'form[action*="medicines"][method="POST"]', function(e) {
        var $form = $(this);
        var actionUrl = $form.attr('action');
        
        // Only handle forms that are for DELETE method (discontinue medicine)
        if ($form.find('input[name="_method"][value="DELETE"]').length > 0) {
            e.preventDefault();
            
            if (!confirm('Discontinue this medicine?')) {
                return;
            }
            
            // Disable submit button to prevent double submission
            var $submitBtn = $form.find('button[type="submit"]');
            var originalText = $submitBtn.html();
            $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Discontinuing...');
            
            $.ajax({
                url: actionUrl,
                method: 'POST',
                data: $form.serialize(),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.success) {
                        showAlert(response.message, 'success');
                        // Refresh the page to show updated data
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showAlert(response.message || 'Operation failed', 'danger');
                    }
                },
                error: function(xhr) {
                    var errorMessage = 'Error performing operation';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showAlert(errorMessage, 'danger');
                },
                complete: function() {
                    // Re-enable submit button
                    $submitBtn.prop('disabled', false).html(originalText);
                }
            });
        }
    });
    
    // Helper function: Save physical count to backend
    function savePhysicalCount(medicineId, physicalCount, $input) {
        const $row = $input.closest('tr');
        const originalBg = $row.css('background-color');
        
        // Visual feedback
        $row.css('background-color', '#fff3cd');
        $input.prop('disabled', true);
        
        $.ajax({
            url: `/medicines/${medicineId}/update-physical-count`,
            method: 'POST',
            data: {
                on_hand_per_count: physicalCount,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Update the shortage/overage display
                    const $shortageCell = $row.find('.shortage-display');
                    const shortageOverage = response.data.shortage_overage;
                    
                    if (shortageOverage === 0 || physicalCount === 0) {
                        $shortageCell.html('<small class="text-muted">-</small>');
                    } else if (shortageOverage > 0) {
                        $shortageCell.html(`<span class="text-success">+${shortageOverage}</span><br><small class="text-muted">Overage</small>`);
                    } else {
                        $shortageCell.html(`<span class="text-danger">${shortageOverage}</span><br><small class="text-muted">Shortage</small>`);
                    }
                    
                    // Update the original value
                    $input.data('original-value', physicalCount);
                    
                    // Show/hide adjust button
                    const $adjustBtn = $row.find('.btn-adjust-stock');
                    if (shortageOverage !== 0) {
                        $adjustBtn.show().data('shortage-overage', shortageOverage);
                    } else {
                        $adjustBtn.hide();
                    }
                    
                    // Success feedback
                    $row.css('background-color', '#d4edda');
                    setTimeout(() => {
                        $row.css('background-color', originalBg);
                    }, 2000);
                    
                    showAlert('Physical count updated successfully.', 'success');
                } else {
                    showAlert(response.message || 'Failed to update physical count.', 'danger');
                    $input.val($input.data('original-value') || 0); // Reset value
                }
            },
            error: function(xhr) {
                let errorMessage = 'Failed to update physical count.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showAlert(errorMessage, 'danger');
                $input.val($input.data('original-value') || 0); // Reset value
            },
            complete: function() {
                $input.prop('disabled', false);
            }
        });
    }
    
    // Helper function: Adjust stock based on physical count
    function adjustStockFromCount(medicineId, reason) {
        $.ajax({
            url: `/medicines/${medicineId}/adjust-stock-from-count`,
            method: 'POST',
            data: {
                adjustment_reason: reason,
                confirm_adjustment: true,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showAlert(response.message, 'success');
                    // Refresh the page to show updated data
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showAlert(response.message || 'Failed to adjust stock.', 'danger');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Failed to adjust stock.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showAlert(errorMessage, 'danger');
            }
        });
    }
});
</script>
@endsection
