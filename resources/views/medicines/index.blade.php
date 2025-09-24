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
                    <p>Total Active Medicines</p>
                </div>
                <div class="icon">
                    <i class="fas fa-pills"></i>
                </div>
                <a href="?status=active" class="small-box-footer">
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
                            <select name="status" id="status" class="form-control">
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
                            <select name="category" id="category" class="form-control">
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
                            <select name="stock_filter" id="stock_filter" class="form-control">
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
                                       value="{{ request('search') }}">
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
                        <tr>
                            <th width="5%">ID</th>
                            <th width="20%">Medicine Details</th>
                            <th width="12%">Category</th>
                            <th width="15%">Stock Info</th>
                            <th width="12%">Therapeutic Info</th>
                            <th width="12%">Expiry</th>
                            <th width="8%">Status</th>
                            <th width="16%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($medicines as $medicine)
                        <tr>
                            <td>{{ $medicine->id }}</td>
                            <td>
                                <strong>{{ $medicine->medicine_name }}</strong>
                                @if($medicine->brand_name)
                                    <br><small class="text-muted">{{ $medicine->brand_name }}</small>
                                @endif
                                @if($medicine->generic_name)
                                    <br><small class="text-primary">Generic: {{ $medicine->generic_name }}</small>
                                @endif
                                <br><small class="text-info">{{ $medicine->strength }} {{ $medicine->dosage_form }}</small>
                                @if($medicine->requires_prescription)
                                    <br><span class="badge badge-warning">Prescription Required</span>
                                @else
                                    <br><span class="badge badge-info">Over-the-Counter</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-secondary">{{ $medicine->category }}</span>
                                @if($medicine->manufacturer)
                                    <br><small class="text-muted mt-1">{{ $medicine->manufacturer }}</small>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $medicine->stock_quantity }} {{ $medicine->unit }}</strong>
                                <br><small class="text-muted">Min: {{ $medicine->minimum_stock }}</small>
                                <br><span class="badge badge-{{ $medicine->stock_status_color }}">{{ $medicine->stock_status }}</span>
                            </td>
                            <td>
                                @if($medicine->therapeutic_class)
                                    <span class="badge badge-info">{{ $medicine->therapeutic_class }}</span>
                                @endif
                                @if($medicine->age_restrictions)
                                    <br><small class="text-warning"><i class="fas fa-user-clock mr-1"></i>{{ $medicine->age_restrictions }}</small>
                                @endif
                                @if($medicine->pregnancy_category)
                                    <br><small class="text-info"><i class="fas fa-baby mr-1"></i>Cat {{ $medicine->pregnancy_category }}</small>
                                @endif
                                @if($medicine->indication)
                                    <br><small class="text-muted" title="{{ $medicine->indication }}">{{ Str::limit($medicine->indication, 30) }}</small>
                                @endif
                            </td>
                            <td>
                                @if($medicine->expiry_date)
                                    {{ $medicine->expiry_date->format('M d, Y') }}
                                    <br>
                                    @if($medicine->is_expired)
                                        <span class="badge badge-danger">Expired</span>
                                    @elseif($medicine->is_expiring_soon)
                                        <span class="badge badge-warning">Expiring Soon</span>
                                    @else
                                        <small class="text-success">{{ $medicine->expiry_date->diffForHumans() }}</small>
                                    @endif
                                @else
                                    <span class="text-muted">No expiry set</span>
                                @endif
                            </td>
                            <td>
                                @if($medicine->status == 'active')
                                    <span class="badge badge-success">Active</span>
                                @elseif($medicine->status == 'inactive')
                                    <span class="badge badge-secondary">Inactive</span>
                                @elseif($medicine->status == 'expired')
                                    <span class="badge badge-danger">Expired</span>
                                @else
                                    <span class="badge badge-dark">Discontinued</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown">
                                        Actions
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('medicines.show', $medicine) }}">
                                            <i class="fas fa-eye mr-2"></i>View Details
                                        </a>
                                        @if($medicine->status != 'discontinued')
                                            <a class="dropdown-item" href="{{ route('medicines.edit', $medicine) }}">
                                                <i class="fas fa-edit mr-2"></i>Edit
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item stock-update" href="#" 
                                               data-id="{{ $medicine->id }}" 
                                               data-name="{{ $medicine->medicine_name }}"
                                               data-current="{{ $medicine->stock_quantity }}"
                                               data-unit="{{ $medicine->unit }}">
                                                <i class="fas fa-boxes mr-2"></i>Update Stock
                                            </a>
                                        @endif
                                        @if($medicine->status == 'active')
                                            <div class="dropdown-divider"></div>
                                            <form action="{{ route('medicines.destroy', $medicine) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Discontinue this medicine?')">
                                                    <i class="fas fa-ban mr-2"></i>Discontinue
                                                </button>
                                            </form>
                                        @endif
                                    </div>
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
        $('#filterForm').submit();
    });

    // DataTable for better sorting
    @if($medicines->count() > 0)
    $('#medicinesTable').DataTable({
        "paging": false,
        "searching": false,
        "ordering": true,
        "info": false,
        "autoWidth": false,
        "responsive": true,
        "order": [[ 1, "asc" ]], // Sort by medicine name
        "columnDefs": [
            { "orderable": false, "targets": [7] } // Disable sorting on actions column
        ]
    });
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
});
</script>
@endsection