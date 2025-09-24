@extends('adminlte::page')

@section('title', 'Stock Management | Bokod CMS')

@section('adminlte_css_pre')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">Stock Management</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('medicines.index') }}">Medicines</a></li>
                <li class="breadcrumb-item active">Stock Management</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <!-- Hidden CSRF Token for AJAX -->
    <input type="hidden" name="_token" value="{{ csrf_token() }}" id="csrf-token">

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

    <!-- Stock Alerts Row -->
    <div class="row mb-3">
        <div class="col-lg-6 col-12">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $lowStockCount }}</h3>
                    <p>Low Stock Medicines</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="small-box-footer">
                    <i class="fas fa-info-circle mr-1"></i>Require immediate attention
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-12">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $outOfStockCount }}</h3>
                    <p>Out of Stock Medicines</p>
                </div>
                <div class="icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="small-box-footer">
                    <i class="fas fa-info-circle mr-1"></i>Critical stock shortage
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions Card -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-cogs mr-2"></i>Bulk Actions</h3>
            <div class="card-tools">
                <a href="{{ route('medicines.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i>Back to Medicines
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <button type="button" class="btn btn-success btn-block" id="bulkAddStock" onclick="testBulkAddStock()">
                        <i class="fas fa-plus mr-2"></i>Bulk Add Stock
                    </button>
                </div>
                <div class="col-md-4">
                    <button type="button" class="btn btn-warning btn-block" id="exportLowStock" onclick="testExportStock()">
                        <i class="fas fa-file-excel mr-2"></i>Export Low Stock Report
                    </button>
                </div>
                <div class="col-md-4">
                    <button type="button" class="btn btn-info btn-block" id="printStockReport" onclick="testPrintStock()">
                        <i class="fas fa-print mr-2"></i>Print Stock Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Management Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-boxes mr-2"></i>Medicine Stock Levels
            </h3>
            <div class="card-tools">
                <div class="input-group input-group-sm" style="width: 200px;">
                    <input type="text" name="table_search" class="form-control float-right" placeholder="Search medicines..." id="stockSearch">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-default">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body table-responsive">
            @if($medicines->count() > 0)
                <table class="table table-bordered table-striped" id="stockTable">
                    <thead>
                        <tr>
                            <th width="5%">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="selectAll">
                                    <label class="custom-control-label" for="selectAll"></label>
                                </div>
                            </th>
                            <th width="25%">Medicine</th>
                            <th width="15%">Current Stock</th>
                            <th width="12%">Min. Stock</th>
                            <th width="15%">Status</th>
                            <th width="13%">Last Updated</th>
                            <th width="15%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($medicines as $medicine)
                        <tr class="{{ $medicine->is_low_stock ? 'table-warning' : '' }}{{ $medicine->stock_quantity <= 0 ? 'table-danger' : '' }}">
                            <td>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input medicine-checkbox" 
                                           id="medicine_{{ $medicine->id }}" value="{{ $medicine->id }}">
                                    <label class="custom-control-label" for="medicine_{{ $medicine->id }}"></label>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($medicine->stock_quantity <= 0)
                                        <i class="fas fa-exclamation-circle text-danger mr-2"></i>
                                    @elseif($medicine->is_low_stock)
                                        <i class="fas fa-exclamation-triangle text-warning mr-2"></i>
                                    @else
                                        <i class="fas fa-check-circle text-success mr-2"></i>
                                    @endif
                                    <div>
                                        <strong>{{ $medicine->medicine_name }}</strong>
                                        @if($medicine->brand_name)
                                            <br><small class="text-muted">{{ $medicine->brand_name }}</small>
                                        @endif
                                        <br><small class="text-info">{{ $medicine->strength }} {{ $medicine->dosage_form }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="stock-quantity" data-id="{{ $medicine->id }}">
                                    <strong class="text-{{ $medicine->stock_quantity <= 0 ? 'danger' : ($medicine->is_low_stock ? 'warning' : 'success') }}">
                                        {{ $medicine->stock_quantity }}
                                    </strong> {{ $medicine->unit ?? 'pcs' }}
                                </span>
                            </td>
                            <td>
                                <span class="text-muted">{{ $medicine->minimum_stock ?? 10 }} {{ $medicine->unit ?? 'pcs' }}</span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $medicine->stock_status_color }}" id="status_{{ $medicine->id }}">
                                    {{ $medicine->stock_status }}
                                </span>
                                @if($medicine->expiry_date && $medicine->is_expiring_soon)
                                    <br><small class="text-warning">
                                        <i class="fas fa-clock mr-1"></i>Exp: {{ $medicine->expiry_date->format('M d, Y') }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $medicine->updated_at->format('M d, Y') }}<br>
                                    {{ $medicine->updated_at->format('h:i A') }}
                                </small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-success stock-add" 
                                            data-id="{{ $medicine->id }}" 
                                            data-name="{{ $medicine->medicine_name }}"
                                            data-current="{{ $medicine->stock_quantity }}"
                                            data-unit="{{ $medicine->unit ?? 'pcs' }}"
                                            onclick="testAddStock({{ $medicine->id }}, {{ json_encode($medicine->medicine_name) }}, {{ $medicine->stock_quantity }}, {{ json_encode($medicine->unit ?? 'pcs') }})">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                    <button type="button" class="btn btn-warning stock-subtract" 
                                            data-id="{{ $medicine->id }}" 
                                            data-name="{{ $medicine->medicine_name }}"
                                            data-current="{{ $medicine->stock_quantity }}"
                                            data-unit="{{ $medicine->unit ?? 'pcs' }}"
                                            onclick="testSubtractStock({{ $medicine->id }}, {{ json_encode($medicine->medicine_name) }}, {{ $medicine->stock_quantity }}, {{ json_encode($medicine->unit ?? 'pcs') }})"
                                            {{ $medicine->stock_quantity <= 0 ? 'disabled' : '' }}>
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-info stock-history" 
                                            data-id="{{ $medicine->id }}" 
                                            data-name="{{ $medicine->medicine_name }}"
                                            onclick="testStockHistory({{ $medicine->id }}, {{ json_encode($medicine->medicine_name) }})">
                                        <i class="fas fa-history"></i>
                                    </button>
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
                    {{ $medicines->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No medicines found for stock management</h5>
                    <p class="text-muted">Add some medicines first to manage their stock levels.</p>
                    <a href="{{ route('medicines.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i>Add New Medicine
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Stock Update Modal -->
    <div class="modal fade" id="quickStockModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Quick Stock Update</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="quickStockForm">
                        <input type="hidden" id="quickMedicineId">
                        <input type="hidden" id="quickOperation">
                        
                        <div class="alert alert-info" id="stockInfo">
                            <i class="fas fa-info-circle mr-2"></i>
                            <span id="operationText"></span> stock for <strong id="quickMedicineName"></strong>
                        </div>
                        
                        <div class="form-group">
                            <label>Current Stock:</label>
                            <p class="form-control-static">
                                <strong id="quickCurrentStock"></strong> <span id="quickStockUnit"></span>
                            </p>
                        </div>
                        
                        <div class="form-group">
                            <label for="quickQuantity">Quantity:</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="quickQuantity" min="1" step="1" required>
                                <div class="input-group-append">
                                    <span class="input-group-text" id="quantityUnit"></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="quickNotes">Notes (optional):</label>
                            <textarea class="form-control" id="quickNotes" rows="2" placeholder="Reason for stock update..."></textarea>
                        </div>
                        
                        <div id="newStockPreview" class="alert alert-secondary" style="display: none;">
                            <strong>New Stock Level:</strong> <span id="previewStock"></span>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmStockUpdate">
                        <i class="fas fa-check mr-2"></i>Update Stock
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Update Modal -->
    <div class="modal fade" id="bulkUpdateModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Bulk Stock Update</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        Selected <span id="selectedCount">0</span> medicine(s) for bulk update
                    </div>
                    
                    <form id="bulkUpdateForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bulkOperation">Operation:</label>
                                    <select class="form-control" id="bulkOperation" required>
                                        <option value="add">Add to Stock</option>
                                        <option value="set">Set Stock Level</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bulkQuantity">Quantity:</label>
                                    <input type="number" class="form-control" id="bulkQuantity" min="0" step="1" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="bulkNotes">Notes:</label>
                            <textarea class="form-control" id="bulkNotes" rows="2" placeholder="Reason for bulk update..."></textarea>
                        </div>
                        
                        <div id="selectedMedicines" class="mt-3">
                            <!-- Selected medicines will be populated here -->
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmBulkUpdate">
                        <i class="fas fa-check mr-2"></i>Update Selected
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stock Input Modal -->
    <div class="modal fade" id="stockInputModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="stockInputTitle">Update Stock</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info" id="stockInputInfo">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span id="stockInputMessage">Update stock for medicine</span>
                    </div>
                    
                    <div class="form-group">
                        <label for="stockInputQuantity">Enter Quantity:</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="stockInputQuantity" min="1" step="1" placeholder="Enter quantity...">
                            <div class="input-group-append">
                                <span class="input-group-text" id="stockInputUnit">pcs</span>
                            </div>
                        </div>
                        <small class="form-text text-muted" id="stockInputHelper">Current stock: 0</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="stockInputConfirm" onclick="confirmStockUpdate()">
                        <i class="fas fa-check mr-2"></i>Update Stock
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Success/Error Modal -->
    <div class="modal fade" id="responseModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" id="responseModalHeader">
                    <h4 class="modal-title" id="responseModalTitle">Operation Result</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center py-3" id="responseModalBody">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h5 id="responseModalMessage">Operation completed successfully!</h5>
                        <p class="text-muted" id="responseModalDetails"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" id="responseModalOk">
                        <i class="fas fa-check mr-2"></i>OK
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="confirmationTitle">Confirm Action</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center py-3">
                        <i class="fas fa-question-circle fa-3x text-warning mb-3"></i>
                        <h5 id="confirmationMessage">Are you sure you want to continue?</h5>
                        <p class="text-muted" id="confirmationDetails"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-warning" id="confirmationConfirm">
                        <i class="fas fa-check mr-2"></i>Continue
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bulk Selection Modal -->
    <div class="modal fade" id="bulkSelectionModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Bulk Stock Update</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span id="bulkSelectionCount">0 medicines selected</span>
                    </div>
                    
                    <div class="form-group">
                        <label for="bulkQuantityInput">Quantity to add to each medicine:</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="bulkQuantityInput" min="1" step="1" placeholder="Enter quantity...">
                            <div class="input-group-append">
                                <span class="input-group-text">units</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h6><i class="fas fa-list mr-2"></i>Selected Medicines:</h6>
                        </div>
                        <div class="card-body" id="bulkSelectedMedicines">
                            <!-- Selected medicines will be listed here -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-success" id="bulkUpdateConfirm">
                        <i class="fas fa-plus mr-2"></i>Update All
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stock History Modal -->
    <div class="modal fade" id="stockHistoryModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Stock History</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="stockHistoryContent">
                        <div class="text-center py-3">
                            <i class="fas fa-spinner fa-spin fa-2x"></i>
                            <p class="mt-2">Loading stock history...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('css')
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
<style>
    .small-box h3 {
        font-size: 2.2rem;
        font-weight: bold;
        margin: 0 0 10px 0;
        white-space: nowrap;
        padding: 0;
    }
    
    .table-warning {
        background-color: #fff3cd !important;
    }
    
    .table-danger {
        background-color: #f8d7da !important;
    }
    
    .btn-group-sm > .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.775rem;
    }
    
    /* Stock History Timeline Styles */
    .timeline {
        position: relative;
        margin: 0 0 30px 0;
        padding: 0;
        list-style: none;
    }
    
    .timeline:before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        left: 31px;
        width: 4px;
        background: #dee2e6;
    }
    
    .timeline > div {
        margin-bottom: 15px;
        position: relative;
    }
    
    .timeline > div > .timeline-item {
        background: #fff;
        border-radius: 3px;
        width: calc(100% - 50px);
        margin-left: 50px;
        margin-right: 15px;
        padding: 10px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12);
    }
    
    .timeline > div > .fas {
        width: 30px;
        height: 30px;
        font-size: 15px;
        line-height: 30px;
        position: absolute;
        color: #666;
        background: #fff;
        border-radius: 50%;
        text-align: center;
        left: 18px;
        top: 0;
        z-index: 1000;
        border: 2px solid #dee2e6;
    }
    
    .timeline-header {
        margin: 0 0 10px 0;
        color: #333;
        font-size: 16px;
        font-weight: 600;
    }
    
    .timeline .time {
        color: #999;
        float: right;
        padding: 10px;
        font-size: 12px;
    }
    
    .time-label > span {
        font-weight: 600;
        color: #fff;
        font-size: 12px;
        padding: 5px 10px;
        display: inline-block;
        border-radius: 4px;
        margin-left: 15px;
    }
    
    .info-box {
        display: block;
        min-height: 90px;
        background: #fff;
        width: 100%;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12);
        border-radius: 2px;
        margin-bottom: 15px;
    }
    
    .info-box .info-box-icon {
        border-top-left-radius: 2px;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 2px;
        display: block;
        float: left;
        height: 90px;
        width: 90px;
        text-align: center;
        font-size: 45px;
        line-height: 90px;
        background: rgba(0,0,0,0.2);
    }
    
    .info-box .info-box-content {
        padding: 5px 10px;
        margin-left: 90px;
    }
    
    .info-box .info-box-number {
        display: block;
        font-weight: bold;
        font-size: 18px;
    }
    
    .info-box .info-box-text {
        display: block;
        font-size: 14px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endsection

@section('adminlte_js')
<script>
console.log('📝 Stock management script loading...');

// Global variables
var currentStockOperation = null;

// Safe modal function with fallbacks
function showModalSafely(modalId) {
    console.log('Showing modal:', modalId);
    var modal = document.getElementById(modalId);
    if (!modal) {
        console.error('Modal not found:', modalId);
        return;
    }
    
    // Try Bootstrap jQuery modal first
    if (typeof $ !== 'undefined' && typeof $.fn.modal !== 'undefined') {
        try {
            $('#' + modalId).modal('show');
            return;
        } catch (e) {
            console.error('Bootstrap modal failed:', e);
        }
    }
    
    // Fallback: Manual display
    modal.style.display = 'block';
    modal.classList.add('show');
    modal.setAttribute('aria-modal', 'true');
    modal.removeAttribute('aria-hidden');
    
    // Add backdrop
    if (!document.getElementById(modalId + '-backdrop')) {
        var backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        backdrop.id = modalId + '-backdrop';
        document.body.appendChild(backdrop);
        document.body.classList.add('modal-open');
    }
}

function hideModalSafely(modalId) {
    var modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        modal.classList.remove('show');
        modal.removeAttribute('aria-modal');
        modal.setAttribute('aria-hidden', 'true');
        
        var backdrop = document.getElementById(modalId + '-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
        document.body.classList.remove('modal-open');
    }
}

// Stock management functions with modals
window.testAddStock = function(id, name, currentStock, unit) {
    console.log('Add stock called:', {id: id, name: name, currentStock: currentStock, unit: unit});
    
    currentStockOperation = {
        medicineId: id,
        medicineName: name,
        currentStock: parseInt(currentStock),
        unit: unit || 'pcs',
        action: 'add'
    };
    
    document.getElementById('stockInputTitle').textContent = 'Add Stock';
    document.getElementById('stockInputMessage').textContent = 'Add stock to ' + name;
    document.getElementById('stockInputQuantity').value = '';
    document.getElementById('stockInputQuantity').removeAttribute('max');
    document.getElementById('stockInputUnit').textContent = unit || 'pcs';
    document.getElementById('stockInputHelper').textContent = 'Current stock: ' + currentStock + ' ' + (unit || 'pcs');
    
    showModalSafely('stockInputModal');
};

window.testSubtractStock = function(id, name, currentStock, unit) {
    console.log('Subtract stock called:', {id: id, name: name, currentStock: currentStock, unit: unit});
    
    currentStock = parseInt(currentStock);
    if (currentStock <= 0) {
        showSimpleModal('error', 'Cannot Subtract', 'Cannot subtract from zero stock!');
        return;
    }
    
    currentStockOperation = {
        medicineId: id,
        medicineName: name,
        currentStock: currentStock,
        unit: unit || 'pcs',
        action: 'subtract'
    };
    
    document.getElementById('stockInputTitle').textContent = 'Subtract Stock';
    document.getElementById('stockInputMessage').textContent = 'Remove stock from ' + name;
    document.getElementById('stockInputQuantity').value = '';
    document.getElementById('stockInputQuantity').setAttribute('max', currentStock);
    document.getElementById('stockInputUnit').textContent = unit || 'pcs';
    document.getElementById('stockInputHelper').textContent = 'Current stock: ' + currentStock + ' ' + (unit || 'pcs');
    
    showModalSafely('stockInputModal');
};

// Stock history - open modal
window.testStockHistory = function(id, name) {
    console.log('Stock history called:', {id: id, name: name});
    
    document.querySelector('#stockHistoryModal .modal-title').textContent = 'Stock History - ' + name;
    document.getElementById('stockHistoryContent').innerHTML = 
        '<div class="text-center py-3"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Loading stock history...</p></div>';
    
    showModalSafely('stockHistoryModal');
    
    // Simple AJAX call
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '/medicines/' + id + '/stock-history', true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            var contentDiv = document.getElementById('stockHistoryContent');
            
            if (xhr.status === 200) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        // Enhanced display with info boxes
                        var medicine = response.medicine;
                        var history = response.history;
                        
                        var html = '<div class="row mb-3">';
                        
                        // Current stock info box
                        html += '<div class="col-md-4"><div class="info-box">';
                        html += '<span class="info-box-icon bg-blue"><i class="fas fa-boxes"></i></span>';
                        html += '<div class="info-box-content">';
                        html += '<span class="info-box-text">Current Stock</span>';
                        html += '<span class="info-box-number">' + medicine.current_stock + ' ' + medicine.unit + '</span>';
                        html += '</div></div></div>';
                        
                        // Total movements info box
                        html += '<div class="col-md-4"><div class="info-box">';
                        html += '<span class="info-box-icon bg-green"><i class="fas fa-history"></i></span>';
                        html += '<div class="info-box-content">';
                        html += '<span class="info-box-text">Total Movements</span>';
                        html += '<span class="info-box-number">' + history.length + '</span>';
                        html += '</div></div></div>';
                        
                        // Minimum stock info box
                        html += '<div class="col-md-4"><div class="info-box">';
                        html += '<span class="info-box-icon bg-orange"><i class="fas fa-exclamation-triangle"></i></span>';
                        html += '<div class="info-box-content">';
                        html += '<span class="info-box-text">Minimum Stock</span>';
                        html += '<span class="info-box-number">' + (medicine.minimum_stock || 10) + ' ' + medicine.unit + '</span>';
                        html += '</div></div></div>';
                        
                        html += '</div>';
                        
                        // Movement details
                        if (history.length > 0) {
                            html += '<h6><i class="fas fa-list mr-2"></i>Stock Movements</h6>';
                            html += '<div class="table-responsive">';
                            html += '<table class="table table-sm table-striped">';
                            html += '<thead><tr>';
                            html += '<th>Type</th><th>Change</th><th>Before</th><th>After</th><th>When</th><th>By</th>';
                            html += '</tr></thead><tbody>';
                            
                            for (var i = 0; i < history.length; i++) {
                                var movement = history[i];
                                var changeAmount = movement.quantity_changed;
                                var typeText = movement.type || 'Update';
                                var userName = movement.user || 'System';
                                var timeText = movement.created_at_human || movement.date || 'Recent';
                                
                                // Visual elements based on movement type
                                var iconClass = 'fas fa-edit text-secondary fa-sm';
                                var changeClass = 'text-secondary';
                                var changeText = changeAmount + ' ' + medicine.unit;
                                
                                if (movement.type === 'add' || movement.type === 'bulk_add') {
                                    iconClass = 'fas fa-plus-circle text-success fa-sm';
                                    changeClass = 'text-success';
                                    changeText = '+' + changeAmount + ' ' + medicine.unit;
                                } else if (movement.type === 'subtract' || movement.type === 'bulk_subtract') {
                                    iconClass = 'fas fa-minus-circle text-warning fa-sm';
                                    changeClass = 'text-warning';
                                    changeText = '-' + changeAmount + ' ' + medicine.unit;
                                }
                                
                                html += '<tr>';
                                html += '<td><i class="' + iconClass + '" style="margin-right: 6px;"></i>' + typeText + '</td>';
                                html += '<td class="' + changeClass + '"><strong>' + changeText + '</strong></td>';
                                html += '<td>' + movement.quantity_before + ' ' + medicine.unit + '</td>';
                                html += '<td>' + movement.quantity_after + ' ' + medicine.unit + '</td>';
                                html += '<td><small>' + timeText + '</small></td>';
                                html += '<td><small>' + userName + '</small></td>';
                                html += '</tr>';
                            }
                            
                            html += '</tbody></table></div>';
                        } else {
                            html += '<div class="alert alert-info">';
                            html += '<i class="fas fa-info-circle mr-2"></i><strong>No stock movements found</strong><br>';
                            html += 'Stock movements will appear here when you add or subtract stock.';
                            html += '</div>';
                        }
                        
                        contentDiv.innerHTML = html;
                    } else {
                        contentDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle mr-2"></i>Failed to load: ' + 
                            (response.message || 'Unknown error') + '</div>';
                    }
                } catch (e) {
                    contentDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle mr-2"></i>Error parsing data.</div>';
                }
            } else {
                contentDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle mr-2"></i>HTTP Error: ' + xhr.status + '</div>';
            }
        }
    };
    
    xhr.send();
};

window.testBulkAddStock = function() {
    console.log('Bulk add stock called');
    
    // Get selected medicines
    var checkboxes = document.querySelectorAll('.medicine-checkbox:checked');
    if (checkboxes.length === 0) {
        showSimpleModal('warning', 'No Selection', 'Please select medicines first by checking the boxes next to medicine names.');
        return;
    }
    
    // Collect selected medicine info first
    var selectedMedicines = [];
    var selectedHtml = '';
    
    checkboxes.forEach(function(checkbox) {
        var row = checkbox.closest('tr');
        var nameElement = row.querySelector('strong');
        var stockElement = row.querySelector('.stock-quantity strong');
        if (nameElement && stockElement) {
            selectedMedicines.push({
                id: checkbox.value,
                name: nameElement.textContent,
                currentStock: stockElement.textContent
            });
            
            selectedHtml += '<div class="row mb-2"><div class="col-8"><strong>' + nameElement.textContent + 
                '</strong></div><div class="col-4 text-right">Current: ' + stockElement.textContent + ' units</div></div>';
        }
    });
    
    // Set up the bulk modal
    document.getElementById('bulkSelectionCount').textContent = selectedMedicines.length + ' medicines selected';
    document.getElementById('bulkSelectedMedicines').innerHTML = selectedHtml;
    document.getElementById('bulkQuantityInput').value = '';
    
    // Store selected medicines globally for the modal
    window.selectedMedicinesForBulk = selectedMedicines;
    
    // Show the bulk selection modal
    showModalSafely('bulkSelectionModal');
};

window.testExportStock = function() {
    console.log('Export stock called');
    showSimpleModal('success', 'Export Started', 'Stock report export will be added next.');
};

window.testPrintStock = function() {
    console.log('Print stock called');
    window.print();
};

// Simple modal function for notifications
function showSimpleModal(type, title, message) {
    var modal = document.getElementById('responseModal');
    if (!modal) {
        console.error('Response modal not found');
        modalError(message, title); // Fallback to universal modal
        return;
    }
    
    var header = document.getElementById('responseModalHeader');
    var titleEl = document.getElementById('responseModalTitle');
    var messageEl = document.getElementById('responseModalMessage');
    var icon = document.querySelector('#responseModalBody i');
    var okBtn = document.getElementById('responseModalOk');
    
    if (header && titleEl && messageEl && icon && okBtn) {
        // Reset classes
        header.className = 'modal-header';
        icon.className = 'fas fa-3x mb-3';
        okBtn.className = 'btn';
        
        // Set styles based on type
        switch(type) {
            case 'success':
                header.classList.add('bg-success');
                icon.classList.add('fa-check-circle', 'text-success');
                okBtn.classList.add('btn-success');
                break;
            case 'error':
                header.classList.add('bg-danger');
                icon.classList.add('fa-exclamation-circle', 'text-danger');
                okBtn.classList.add('btn-danger');
                break;
            case 'warning':
                header.classList.add('bg-warning');
                icon.classList.add('fa-exclamation-triangle', 'text-warning');
                okBtn.classList.add('btn-warning');
                break;
            case 'info':
                header.classList.add('bg-info');
                icon.classList.add('fa-info-circle', 'text-info');
                okBtn.classList.add('btn-info');
                break;
        }
        
        titleEl.textContent = title;
        messageEl.textContent = message;
    }
    
    showModalSafely('responseModal');
}

// Confirmation handler for stock updates
window.confirmStockUpdate = function() {
    if (!currentStockOperation) {
        showSimpleModal('error', 'Error', 'No operation data found');
        return;
    }
    
    var quantity = parseInt(document.getElementById('stockInputQuantity').value);
    if (!quantity || quantity < 1) {
        showSimpleModal('warning', 'Invalid Quantity', 'Please enter a valid quantity greater than 0');
        return;
    }
    
    if (currentStockOperation.action === 'subtract' && quantity > currentStockOperation.currentStock) {
        showSimpleModal('error', 'Insufficient Stock', 
            'Cannot subtract more than current stock (' + currentStockOperation.currentStock + ')');
        return;
    }
    
    hideModalSafely('stockInputModal');
    
    // Perform real stock update via AJAX
    performStockUpdate(
        currentStockOperation.medicineId,
        currentStockOperation.action,
        quantity,
        'Manual stock ' + currentStockOperation.action
    );
};

// AJAX function to perform real stock updates
function performStockUpdate(medicineId, action, quantity, reason) {
    console.log('Performing stock update:', { medicineId, action, quantity, reason });
    
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '/medicines/' + medicineId + '/update-stock', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        showSimpleModal('success', 'Stock Updated Successfully!', 
                            response.message + '\n\nNew stock quantity: ' + response.new_quantity + ' units');
                        // Reload page after 2 seconds to show updated stock
                        setTimeout(function() { 
                            location.reload(); 
                        }, 2000);
                    } else {
                        showSimpleModal('error', 'Update Failed', response.message || 'Unknown error occurred');
                    }
                } catch (e) {
                    console.error('Failed to parse response:', e);
                    showSimpleModal('error', 'Error', 'Invalid response from server.');
                }
            } else {
                showSimpleModal('error', 'Request Failed', 'HTTP Error: ' + xhr.status + '. Please try again.');
            }
        }
    };
    
    var params = '_token=' + encodeURIComponent(csrfToken) +
                 '&action=' + encodeURIComponent(action) + 
                 '&quantity=' + encodeURIComponent(quantity) + 
                 '&reason=' + encodeURIComponent(reason);
    
    xhr.send(params);
}

// Bulk stock update function
function performBulkStockUpdate(selectedMedicines, quantity) {
    console.log('Performing bulk update:', { selectedMedicines, quantity });
    
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var updates = selectedMedicines.map(function(med) {
        return { 
            medicine_id: parseInt(med.id), 
            action: 'add', 
            quantity: quantity 
        };
    });
    
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '/medicines/bulk-update-stock', true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        showSimpleModal('success', 'Bulk Update Successful!', 
                            'Successfully updated stock for ' + selectedMedicines.length + ' medicines.\n\n' + response.message);
                        setTimeout(function() { location.reload(); }, 3000);
                    } else {
                        showSimpleModal('error', 'Bulk Update Failed', response.message || 'Unknown error occurred');
                    }
                } catch (e) {
                    console.error('Bulk update error:', e);
                    showSimpleModal('error', 'Error', 'Invalid response from server.');
                }
            } else {
                showSimpleModal('error', 'Request Failed', 'HTTP Error: ' + xhr.status + '. Please try again.');
            }
        }
    };
    
    xhr.send(JSON.stringify({
        updates: updates,
        reason: 'Bulk stock addition via interface'
    }));
}

// Modal handler for bulk update confirmation
function handleBulkUpdateConfirm() {
    var quantity = parseInt(document.getElementById('bulkQuantityInput').value);
    
    if (!quantity || quantity <= 0) {
        showSimpleModal('error', 'Invalid Quantity', 'Please enter a valid positive number.');
        return;
    }
    
    if (!window.selectedMedicinesForBulk || window.selectedMedicinesForBulk.length === 0) {
        showSimpleModal('error', 'No Selection', 'No medicines selected for bulk update.');
        return;
    }
    
    // Hide the bulk modal
    hideModalSafely('bulkSelectionModal');
    
    // Perform the bulk update
    performBulkStockUpdate(window.selectedMedicinesForBulk, quantity);
}

// Initialize modal handlers when page loads
if (typeof $ !== 'undefined') {
    $(document).ready(function() {
        // Bulk update confirm button
        $('#bulkUpdateConfirm').on('click', function() {
            handleBulkUpdateConfirm();
        });
        
        // Enter key support in quantity input
        $('#bulkQuantityInput').keypress(function(e) {
            if (e.which === 13) {
                handleBulkUpdateConfirm();
            }
        });
        
        // Select All functionality
        $('#selectAll').change(function() {
            var isChecked = $(this).prop('checked');
            $('.medicine-checkbox').prop('checked', isChecked);
        });
        
        // Update Select All when individual checkboxes change
        $('.medicine-checkbox').change(function() {
            var totalCheckboxes = $('.medicine-checkbox').length;
            var checkedCheckboxes = $('.medicine-checkbox:checked').length;
            $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
        });
    });
} else {
    // Fallback for when jQuery is not available
    setTimeout(function() {
        var confirmBtn = document.getElementById('bulkUpdateConfirm');
        if (confirmBtn) {
            confirmBtn.onclick = handleBulkUpdateConfirm;
        }
        
        var quantityInput = document.getElementById('bulkQuantityInput');
        if (quantityInput) {
            quantityInput.addEventListener('keypress', function(e) {
                if (e.which === 13 || e.keyCode === 13) {
                    handleBulkUpdateConfirm();
                }
            });
        }
        
        // Select All functionality (vanilla JS)
        var selectAllCheckbox = document.getElementById('selectAll');
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                var isChecked = this.checked;
                var medicineCheckboxes = document.querySelectorAll('.medicine-checkbox');
                medicineCheckboxes.forEach(function(checkbox) {
                    checkbox.checked = isChecked;
                });
            });
        }
        
        // Update Select All when individual checkboxes change (vanilla JS)
        var medicineCheckboxes = document.querySelectorAll('.medicine-checkbox');
        medicineCheckboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                var totalCheckboxes = document.querySelectorAll('.medicine-checkbox').length;
                var checkedCheckboxes = document.querySelectorAll('.medicine-checkbox:checked').length;
                var selectAll = document.getElementById('selectAll');
                if (selectAll) {
                    selectAll.checked = (totalCheckboxes === checkedCheckboxes);
                }
            });
        });
    }, 500);
}

console.log('✅ Stock management functions loaded');
console.log('Function availability check:', {
    testAddStock: typeof window.testAddStock,
    testSubtractStock: typeof window.testSubtractStock,
    testStockHistory: typeof window.testStockHistory,
    testBulkAddStock: typeof window.testBulkAddStock,
    testExportStock: typeof window.testExportStock,
    testPrintStock: typeof window.testPrintStock,
    confirmStockUpdate: typeof window.confirmStockUpdate
});
</script>
@endsection
