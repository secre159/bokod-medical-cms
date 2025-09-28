@extends('adminlte::page')

@section('title', 'Medicine Management - Documentation')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-pills"></i> Medicine & Prescription Management
            <small class="text-muted">Complete step-by-step medicine and prescription guide</small>
        </h1>
        <a href="{{ route('admin.documentation.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Documentation
        </a>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-3">
        <!-- Table of Contents -->
        <div class="sticky-toc">
            <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list"></i> Table of Contents</h3>
            </div>
            <div class="card-body p-0">
                <ul class="nav nav-pills flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="#add-medicine">Adding Medicines</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#stock-management">Stock Management</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#create-prescription">Creating Prescriptions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#dispense-medication">Dispensing Medication</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#inventory-tracking">Inventory Tracking</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#low-stock-alerts">Low Stock Alerts</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#reports-analytics">Reports & Analytics</a>
                    </li>
                </ul>
            </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-9">
        <!-- Add Medicine Section -->
        <div class="card" id="add-medicine">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-plus-circle"></i> Adding Medicines to Inventory - Step by Step</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> <strong>Before You Start:</strong> 
                    Gather all medicine information including generic name, dosage, manufacturer details, and initial stock quantity.
                </div>

                <h5><i class="fas fa-step-forward text-primary"></i> Step 1: Access Medicine Creation Form</h5>
                <div class="card border-primary mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Navigate to Medicines:</strong> Click "Medicines" in the left sidebar</li>
                            <li><strong>Click "Add Medicine"</strong> button (usually green button in top-right)</li>
                            <li><strong>Form Opens:</strong> Medicine registration form appears</li>
                        </ol>
                    </div>
                </div>

                <h5><i class="fas fa-step-forward text-success"></i> Step 2: Enter Medicine Details</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <p><strong>Required Information:</strong></p>
                        <ol>
                            <li><strong>Medicine Name:</strong> Brand or trade name (e.g., "Tylenol")</li>
                            <li><strong>Generic Name:</strong> Active ingredient (e.g., "Acetaminophen")</li>
                            <li><strong>Dosage Form:</strong>
                                <ul>
                                    <li>Tablet, Capsule, Syrup, Injection, etc.</li>
                                    <li>Select from dropdown or enter if not listed</li>
                                </ul>
                            </li>
                            <li><strong>Strength/Concentration:</strong> (e.g., "500mg", "250mg/5ml")</li>
                            <li><strong>Manufacturer:</strong> Company that produces the medicine</li>
                        </ol>
                        
                        <p><strong>Optional but Recommended:</strong></p>
                        <ul>
                            <li>Medicine category (Antibiotic, Painkiller, etc.)</li>
                            <li>Description or notes</li>
                            <li>Storage requirements</li>
                            <li>Prescription requirements (Rx or OTC)</li>
                        </ul>
                    </div>
                </div>

                <h5><i class="fas fa-step-forward text-info"></i> Step 3: Set Pricing and Stock Information</h5>
                <div class="card border-info mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Unit Price:</strong> Cost per unit (tablet, ml, etc.)</li>
                            <li><strong>Initial Stock Quantity:</strong> How many units you're adding</li>
                            <li><strong>Minimum Stock Level:</strong> Alert threshold for low stock</li>
                            <li><strong>Maximum Stock Level:</strong> Optional - for ordering limits</li>
                            <li><strong>Expiration Date:</strong> If applicable for current batch</li>
                        </ol>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> <strong>Important:</strong> 
                            Double-check dosage and strength information - errors can be dangerous!
                        </div>
                    </div>
                </div>

                <h5><i class="fas fa-step-forward text-success"></i> Step 4: Save and Verify</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Review All Information:</strong> Double-check all entered data</li>
                            <li><strong>Click "Add Medicine"</strong> button</li>
                            <li><strong>Success Message:</strong> System confirms medicine addition</li>
                            <li><strong>Medicine Appears:</strong> In medicine list with assigned ID</li>
                            <li><strong>Stock Updated:</strong> Inventory reflects new stock quantity</li>
                        </ol>
                        
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> <strong>Success!</strong> 
                            Medicine is now in the system and available for prescribing.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Management Section -->
        <div class="card" id="stock-management">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-boxes"></i> Stock Management</h3>
            </div>
            <div class="card-body">
                <h5><i class="fas fa-plus text-success"></i> Adding Stock (Restocking)</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Go to Stock Management:</strong> Click "Medicines" → "Stock Management"</li>
                            <li><strong>Find the Medicine:</strong> Use search or browse the list</li>
                            <li><strong>Click "Update Stock"</strong> button next to medicine</li>
                            <li><strong>Enter Details:</strong>
                                <ul>
                                    <li>Quantity to add</li>
                                    <li>Purchase date</li>
                                    <li>Supplier information</li>
                                    <li>Batch number (if applicable)</li>
                                    <li>Expiration date</li>
                                </ul>
                            </li>
                            <li><strong>Reason:</strong> Select "Restocking" or "Purchase"</li>
                            <li><strong>Save:</strong> Click "Update Stock" to confirm</li>
                        </ol>
                    </div>
                </div>

                <h5><i class="fas fa-minus text-warning"></i> Adjusting Stock (Corrections)</h5>
                <div class="card border-warning mb-3">
                    <div class="card-body">
                        <p><strong>When to make adjustments:</strong></p>
                        <ul>
                            <li>Physical count doesn't match system count</li>
                            <li>Expired medicines need to be removed</li>
                            <li>Damaged stock needs to be written off</li>
                            <li>Theft or loss needs to be recorded</li>
                        </ul>
                        
                        <p><strong>How to adjust:</strong></p>
                        <ol>
                            <li><strong>Access the Medicine:</strong> Find in stock management</li>
                            <li><strong>Click "Adjust Stock"</strong></li>
                            <li><strong>Enter Adjustment:</strong>
                                <ul>
                                    <li>Use positive numbers to add</li>
                                    <li>Use negative numbers to subtract</li>
                                </ul>
                            </li>
                            <li><strong>Provide Reason:</strong> Required - explain the adjustment</li>
                            <li><strong>Save Changes:</strong> Confirm the adjustment</li>
                        </ol>
                    </div>
                </div>

                <h5><i class="fas fa-search text-info"></i> Viewing Stock History</h5>
                <div class="card border-info mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Find Medicine:</strong> In stock management list</li>
                            <li><strong>Click "History" button</strong> (clock icon)</li>
                            <li><strong>View Transactions:</strong> Complete log of all stock changes</li>
                            <li><strong>Information Shown:</strong>
                                <ul>
                                    <li>Date and time of change</li>
                                    <li>Type of transaction (purchase, dispensing, adjustment)</li>
                                    <li>Quantity changed</li>
                                    <li>User who made the change</li>
                                    <li>Reason for change</li>
                                </ul>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Prescription Section -->
        <div class="card" id="create-prescription">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-prescription-bottle-alt"></i> Creating Prescriptions</h3>
            </div>
            <div class="card-body">
                <h5><i class="fas fa-step-forward text-primary"></i> Step 1: Access Prescription Creation</h5>
                <div class="card border-primary mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Navigate to Prescriptions:</strong> Click "Prescriptions" in sidebar</li>
                            <li><strong>Click "Create Prescription"</strong> button</li>
                            <li><strong>Alternative:</strong> Create from patient profile or appointment</li>
                        </ol>
                    </div>
                </div>

                <h5><i class="fas fa-step-forward text-success"></i> Step 2: Select Patient</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Patient Search:</strong> Start typing patient's name</li>
                            <li><strong>Select Patient:</strong> Choose from dropdown results</li>
                            <li><strong>Verify Selection:</strong> Check patient details displayed</li>
                            <li><strong>Review Allergies:</strong> System shows known drug allergies</li>
                        </ol>
                        
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> <strong>Critical:</strong> 
                            Always check patient allergies before prescribing any medication!
                        </div>
                    </div>
                </div>

                <h5><i class="fas fa-step-forward text-info"></i> Step 3: Add Medications</h5>
                <div class="card border-info mb-3">
                    <div class="card-body">
                        <p><strong>For each medication:</strong></p>
                        <ol>
                            <li><strong>Search Medicine:</strong> Type medicine name or generic name</li>
                            <li><strong>Select Medicine:</strong> Choose from available medicines</li>
                            <li><strong>Set Dosage:</strong>
                                <ul>
                                    <li>Dosage per unit (e.g., "1 tablet")</li>
                                    <li>Frequency (e.g., "3 times daily")</li>
                                    <li>Duration (e.g., "7 days")</li>
                                </ul>
                            </li>
                            <li><strong>Quantity to Dispense:</strong> Total number of units</li>
                            <li><strong>Instructions:</strong> How to take the medicine</li>
                            <li><strong>Add Medicine:</strong> Click "Add to Prescription"</li>
                        </ol>
                        
                        <p><strong>Repeat for additional medications</strong></p>
                    </div>
                </div>

                <h5><i class="fas fa-step-forward text-warning"></i> Step 4: Add Prescription Details</h5>
                <div class="card border-warning mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Diagnosis/Condition:</strong> Why the prescription is needed</li>
                            <li><strong>General Instructions:</strong> Overall guidance for patient</li>
                            <li><strong>Follow-up Required:</strong> If patient needs to return</li>
                            <li><strong>Refills Allowed:</strong> Number of refills permitted</li>
                            <li><strong>Valid Until:</strong> Prescription expiration date</li>
                        </ol>
                    </div>
                </div>

                <h5><i class="fas fa-step-forward text-success"></i> Step 5: Review and Save</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Review All Medications:</strong> Check dosages and quantities</li>
                            <li><strong>Verify Instructions:</strong> Ensure clarity for patient</li>
                            <li><strong>Check Drug Interactions:</strong> System may show warnings</li>
                            <li><strong>Save Prescription:</strong> Click "Create Prescription"</li>
                            <li><strong>Print/Send:</strong> Generate prescription for patient</li>
                        </ol>
                        
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> <strong>Success!</strong> 
                            Prescription created and ready for dispensing.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dispense Medication Section -->
        <div class="card" id="dispense-medication">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-hand-holding-medical"></i> Dispensing Medication</h3>
            </div>
            <div class="card-body">
                <h5><i class="fas fa-step-forward text-primary"></i> Finding Prescriptions to Dispense</h5>
                <div class="card border-primary mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Go to Prescriptions:</strong> Click "Prescriptions" in sidebar</li>
                            <li><strong>Filter by Status:</strong> Select "Pending" to see undispensed prescriptions</li>
                            <li><strong>Find Prescription:</strong> Search by patient name or prescription ID</li>
                            <li><strong>Click "Dispense"</strong> button next to prescription</li>
                        </ol>
                    </div>
                </div>

                <h5><i class="fas fa-step-forward text-success"></i> Dispensing Process</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <p><strong>For each medication in prescription:</strong></p>
                        <ol>
                            <li><strong>Check Stock:</strong> Verify sufficient quantity available</li>
                            <li><strong>Confirm Quantity:</strong> Amount to dispense</li>
                            <li><strong>Select Batch:</strong> If multiple batches, choose appropriate one</li>
                            <li><strong>Check Expiration:</strong> Ensure medicine not expired</li>
                            <li><strong>Mark as Dispensed:</strong> Check the checkbox</li>
                        </ol>
                        
                        <p><strong>Complete Dispensing:</strong></p>
                        <ol>
                            <li><strong>Add Notes:</strong> Any special instructions or observations</li>
                            <li><strong>Patient Counseling:</strong> Record if counseling provided</li>
                            <li><strong>Click "Complete Dispensing"</strong></li>
                        </ol>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> <strong>Auto Updates:</strong> 
                            Stock quantities automatically decrease when medicines are dispensed.
                        </div>
                    </div>
                </div>

                <h5><i class="fas fa-exclamation-triangle text-warning"></i> Partial Dispensing</h5>
                <div class="card border-warning mb-3">
                    <div class="card-body">
                        <p><strong>When stock is insufficient:</strong></p>
                        <ol>
                            <li><strong>Partial Quantity:</strong> Enter available quantity</li>
                            <li><strong>Mark as Partial:</strong> Select "Partial Dispensing" option</li>
                            <li><strong>Add Note:</strong> Explain reason for partial dispensing</li>
                            <li><strong>Save:</strong> Prescription remains "Partially Fulfilled"</li>
                            <li><strong>Follow-up:</strong> Complete when stock replenished</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Tracking Section -->
        <div class="card" id="inventory-tracking">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-clipboard-list"></i> Inventory Tracking</h3>
            </div>
            <div class="card-body">
                <h5><i class="fas fa-chart-line text-primary"></i> Monitoring Stock Levels</h5>
                <div class="card border-primary mb-3">
                    <div class="card-body">
                        <p><strong>Regular Monitoring Tasks:</strong></p>
                        <ul>
                            <li><strong>Daily:</strong> Check low stock alerts</li>
                            <li><strong>Weekly:</strong> Review fast-moving medicines</li>
                            <li><strong>Monthly:</strong> Physical stock count verification</li>
                            <li><strong>Quarterly:</strong> Full inventory audit</li>
                        </ul>
                        
                        <p><strong>Access Inventory Reports:</strong></p>
                        <ol>
                            <li>Go to "Medicines" → "Stock Management"</li>
                            <li>Use filters to view specific categories</li>
                            <li>Click "Export" for detailed reports</li>
                        </ol>
                    </div>
                </div>

                <h5><i class="fas fa-calculator text-success"></i> Physical Stock Count</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <p><strong>Monthly Physical Count Process:</strong></p>
                        <ol>
                            <li><strong>Print Stock List:</strong> Get current system quantities</li>
                            <li><strong>Count Physical Stock:</strong> Manually count each medicine</li>
                            <li><strong>Record Differences:</strong> Note any discrepancies</li>
                            <li><strong>Update System:</strong> Make adjustments for differences</li>
                            <li><strong>Investigate Variances:</strong> Find reasons for discrepancies</li>
                        </ol>
                        
                        <p><strong>Using Physical Count Feature:</strong></p>
                        <ol>
                            <li>Go to "Medicines" → "Physical Count"</li>
                            <li>Enter actual counted quantities</li>
                            <li>System shows differences</li>
                            <li>Approve adjustments</li>
                        </ol>
                    </div>
                </div>

                <h5><i class="fas fa-calendar-times text-danger"></i> Expiration Date Management</h5>
                <div class="card border-danger mb-3">
                    <div class="card-body">
                        <p><strong>Managing Expiring Medicines:</strong></p>
                        <ol>
                            <li><strong>Check Expiration Report:</strong> Monthly review of expiring medicines</li>
                            <li><strong>Identify Near Expiry:</strong> Medicines expiring in 3-6 months</li>
                            <li><strong>Use FIFO Principle:</strong> First In, First Out dispensing</li>
                            <li><strong>Mark Expired:</strong> Update status for expired medicines</li>
                            <li><strong>Remove from Stock:</strong> Adjust quantities for expired items</li>
                        </ol>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> <strong>Safety:</strong> 
                            Never dispense expired medications - patient safety is paramount!
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Alerts Section -->
        <div class="card" id="low-stock-alerts">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-exclamation-triangle"></i> Low Stock Alerts</h3>
            </div>
            <div class="card-body">
                <h5><i class="fas fa-bell text-warning"></i> Setting Up Stock Alerts</h5>
                <div class="card border-warning mb-3">
                    <div class="card-body">
                        <p><strong>Configuring Alert Thresholds:</strong></p>
                        <ol>
                            <li><strong>Edit Medicine:</strong> Go to medicine details</li>
                            <li><strong>Set Minimum Level:</strong> Enter quantity that triggers alert</li>
                            <li><strong>Save Changes:</strong> Update medicine information</li>
                            <li><strong>System Monitors:</strong> Automatic checking against threshold</li>
                        </ol>
                        
                        <p><strong>Recommended Thresholds:</strong></p>
                        <ul>
                            <li><strong>Fast-moving medicines:</strong> 2-week supply</li>
                            <li><strong>Regular medicines:</strong> 1-week supply</li>
                            <li><strong>Slow-moving medicines:</strong> 5-day supply</li>
                            <li><strong>Emergency medicines:</strong> Always maintain buffer stock</li>
                        </ul>
                    </div>
                </div>

                <h5><i class="fas fa-eye text-info"></i> Viewing and Managing Alerts</h5>
                <div class="card border-info mb-3">
                    <div class="card-body">
                        <p><strong>Checking Low Stock Alerts:</strong></p>
                        <ol>
                            <li><strong>Dashboard View:</strong> Shows count of low stock items</li>
                            <li><strong>Stock Management:</strong> Filter by "Low Stock" status</li>
                            <li><strong>Email Alerts:</strong> Automatic notifications if configured</li>
                        </ol>
                        
                        <p><strong>Taking Action on Alerts:</strong></p>
                        <ol>
                            <li><strong>Review Alert List:</strong> Identify priority medicines</li>
                            <li><strong>Check Usage Trends:</strong> Verify if restocking needed</li>
                            <li><strong>Create Purchase Order:</strong> Order new stock</li>
                            <li><strong>Update Status:</strong> Mark as "Ordered" if applicable</li>
                        </ol>
                    </div>
                </div>

                <h5><i class="fas fa-shopping-cart text-success"></i> Reordering Process</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Generate Reorder Report:</strong> List of medicines to purchase</li>
                            <li><strong>Contact Suppliers:</strong> Get quotes and availability</li>
                            <li><strong>Place Orders:</strong> Submit purchase orders</li>
                            <li><strong>Track Orders:</strong> Monitor delivery status</li>
                            <li><strong>Update Upon Arrival:</strong> Add new stock when received</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reports and Analytics Section -->
        <div class="card" id="reports-analytics">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-bar"></i> Medicine Reports & Analytics</h3>
            </div>
            <div class="card-body">
                <h5><i class="fas fa-file-alt text-primary"></i> Available Reports</h5>
                <div class="card border-primary mb-3">
                    <div class="card-body">
                        <p><strong>Stock Reports:</strong></p>
                        <ul>
                            <li><strong>Current Stock Levels:</strong> All medicines with quantities</li>
                            <li><strong>Low Stock Report:</strong> Medicines below minimum threshold</li>
                            <li><strong>Expiring Medicines:</strong> Items expiring soon</li>
                            <li><strong>Stock Movement:</strong> Additions and dispensing history</li>
                        </ul>
                        
                        <p><strong>Prescription Reports:</strong></p>
                        <ul>
                            <li><strong>Daily Dispensing:</strong> Medicines dispensed per day</li>
                            <li><strong>Top Prescribed:</strong> Most frequently prescribed medicines</li>
                            <li><strong>Patient Prescription History:</strong> Individual patient reports</li>
                            <li><strong>Prescription Trends:</strong> Usage patterns over time</li>
                        </ul>
                    </div>
                </div>

                <h5><i class="fas fa-chart-line text-success"></i> Generating Reports</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Access Reports:</strong> Go to "Reports" → "Medicine Reports"</li>
                            <li><strong>Select Report Type:</strong> Choose from available options</li>
                            <li><strong>Set Parameters:</strong>
                                <ul>
                                    <li>Date range</li>
                                    <li>Specific medicines or categories</li>
                                    <li>Filtering options</li>
                                </ul>
                            </li>
                            <li><strong>Generate Report:</strong> Click "Generate" button</li>
                            <li><strong>Export Options:</strong> PDF, Excel, or print</li>
                        </ol>
                    </div>
                </div>

                <h5><i class="fas fa-analytics text-info"></i> Key Performance Indicators</h5>
                <div class="card border-info mb-3">
                    <div class="card-body">
                        <p><strong>Important Metrics to Monitor:</strong></p>
                        <ul>
                            <li><strong>Stock Turnover Rate:</strong> How quickly medicines are used</li>
                            <li><strong>Expired Medicine Percentage:</strong> Waste due to expiration</li>
                            <li><strong>Average Days to Dispense:</strong> Time from prescription to dispensing</li>
                            <li><strong>Stock-out Frequency:</strong> How often medicines run out</li>
                            <li><strong>Cost per Prescription:</strong> Average value of prescriptions</li>
                        </ul>
                        
                        <p><strong>Using Analytics for Decision Making:</strong></p>
                        <ul>
                            <li>Adjust minimum stock levels based on usage</li>
                            <li>Identify slow-moving medicines</li>
                            <li>Optimize purchasing decisions</li>
                            <li>Improve patient service levels</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
/* Sticky Table of Contents */
.sticky-toc {
    position: -webkit-sticky;
    position: sticky;
    top: 20px;
    max-height: calc(100vh - 40px);
    overflow-y: auto;
    z-index: 100;
}

.sticky-toc .card {
    margin-bottom: 0;
}

.nav-pills .nav-link {
    color: #495057;
    border-radius: 0.25rem;
    margin-bottom: 0.25rem;
}

.nav-pills .nav-link:hover {
    background-color: #f8f9fa;
    color: #007bff;
}

.nav-pills .nav-link.active {
    background-color: #007bff;
    color: white;
}

.card {
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.alert {
    border-left: 4px solid;
}

.alert-info {
    border-left-color: #17a2b8;
}

.alert-success {
    border-left-color: #28a745;
}

.alert-warning {
    border-left-color: #ffc107;
}

.alert-danger {
    border-left-color: #dc3545;
}

h5 {
    margin-top: 1.5rem;
    margin-bottom: 1rem;
}

.card .card-body ul {
    padding-left: 1.5rem;
}

.card .card-body li {
    margin-bottom: 0.5rem;
}

.text-primary { color: #007bff !important; }
.text-success { color: #28a745 !important; }
.text-warning { color: #ffc107 !important; }
.text-danger { color: #dc3545 !important; }
.text-info { color: #17a2b8 !important; }
.text-secondary { color: #6c757d !important; }

/* Responsive adjustments */
@media (max-width: 768px) {
    .sticky-toc {
        position: relative;
        top: auto;
        max-height: none;
        overflow-y: visible;
    }
}
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Smooth scroll for table of contents links
    $('.nav-link').click(function(e) {
        e.preventDefault();
        var target = $(this).attr('href');
        if (target.startsWith('#')) {
            $('html, body').animate({
                scrollTop: $(target).offset().top - 100
            }, 500);
        }
    });

    // Highlight active section in table of contents
    $(window).scroll(function() {
        var scrollPos = $(window).scrollTop() + 150;
        $('.nav-link').removeClass('active');
        
        $('div[id]').each(function() {
            var currLink = $('a[href="#' + $(this).attr('id') + '"]');
            if ($(this).offset().top <= scrollPos && $(this).offset().top + $(this).height() > scrollPos) {
                currLink.addClass('active');
            }
        });
    });
});
</script>
@stop
