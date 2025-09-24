@extends('adminlte::page')

@section('title', 'Reports Dashboard | Bokod CMS')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">Reports Dashboard</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                <li class="breadcrumb-item active">Reports</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')

    <!-- Date Range Filter -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-calendar-alt mr-2"></i>Report Filters
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <form id="reportFilters" class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="date_from">From Date</label>
                        <input type="date" id="date_from" class="form-control" value="{{ date('Y-m-01') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="date_to">To Date</label>
                        <input type="date" id="date_to" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="report_type">Report Type</label>
                        <select id="report_type" class="form-control">
                            <option value="overview" {{ ($initialType ?? '') == 'overview' ? 'selected' : '' }}>Overview</option>
                            <option value="patients" {{ ($initialType ?? '') == 'patients' ? 'selected' : '' }}>Patients</option>
                            <option value="prescriptions" {{ ($initialType ?? '') == 'prescriptions' ? 'selected' : '' }}>Prescriptions</option>
                            <option value="medicines" {{ ($initialType ?? '') == 'medicines' ? 'selected' : '' }}>Medicines</option>
                            <option value="financial" {{ ($initialType ?? '') == 'financial' ? 'selected' : '' }}>Financial</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div>
                            <button type="button" id="applyFilters" class="btn btn-primary">
                                <i class="fas fa-search mr-1"></i>Apply Filters
                            </button>
                            <button type="button" id="exportReport" class="btn btn-success">
                                <i class="fas fa-download mr-1"></i>Export
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Overview Statistics -->
    <div class="row" id="overviewStats">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="totalPatients">{{ $stats['patients']['total'] ?? 0 }}</h3>
                    <p>Total Patients</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('reports.patients') }}" class="small-box-footer">
                    View Details <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 id="totalPrescriptions">{{ $stats['prescriptions']['total'] ?? 0 }}</h3>
                    <p>Total Prescriptions</p>
                </div>
                <div class="icon">
                    <i class="fas fa-prescription-bottle-alt"></i>
                </div>
                <a href="{{ route('reports.prescriptions') }}" class="small-box-footer">
                    View Details <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3 id="totalMedicines">{{ $stats['medicines']['total'] ?? 0 }}</h3>
                    <p>Total Medicines</p>
                </div>
                <div class="icon">
                    <i class="fas fa-pills"></i>
                </div>
                <a href="{{ route('reports.medicines') }}" class="small-box-footer">
                    View Details <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3 id="lowStockAlerts">{{ $stats['medicines']['low_stock'] ?? 0 }}</h3>
                    <p>Low Stock Alerts</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <a href="{{ route('medicines.stock') }}" class="small-box-footer">
                    View Details <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Patient Demographics Chart -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie mr-2"></i>Patient Demographics
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="patientDemographicsChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Prescription Trends Chart -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line mr-2"></i>Prescription Trends
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="prescriptionTrendsChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Top Medicines Chart -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar mr-2"></i>Top Prescribed Medicines
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="topMedicinesChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Medicine Categories Chart -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-doughnut mr-2"></i>Medicine Categories
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="medicineCategoriesChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Reports Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-table mr-2"></i>Detailed Reports
            </h3>
            <div class="card-tools">
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown">
                        <i class="fas fa-download mr-1"></i>Export
                    </button>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#" onclick="exportReport('pdf')">
                            <i class="fas fa-file-pdf mr-2"></i>PDF
                        </a>
                        <a class="dropdown-item" href="#" onclick="exportReport('excel')">
                            <i class="fas fa-file-excel mr-2"></i>Excel
                        </a>
                        <a class="dropdown-item" href="#" onclick="exportReport('csv')">
                            <i class="fas fa-file-csv mr-2"></i>CSV
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div id="reportsTable">
                <!-- Dynamic content will be loaded here -->
                <div class="text-center py-5">
                    <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Select report type and apply filters to view detailed data</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-user-plus"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">New Patients This Month</span>
                    <span class="info-box-number" id="newPatientsMonth">{{ $stats['patients']['new_this_month'] ?? 0 }}</span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-prescription"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Prescriptions Today</span>
                    <span class="info-box-number" id="prescriptionsToday">{{ $stats['prescriptions']['today'] ?? 0 }}</span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-money-bill-wave"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Revenue This Month</span>
                    <span class="info-box-number" id="revenueMonth">₱{{ number_format($stats['financial']['revenue_month'] ?? 0, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-danger"><i class="fas fa-calendar-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Appointments Today</span>
                    <span class="info-box-number" id="appointmentsToday">{{ $stats['appointments']['today'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

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
    
    .info-box {
        display: block;
        min-height: 90px;
        background: #fff;
        width: 100%;
        box-shadow: 0 1px 1px rgba(0,0,0,.1);
        border-radius: .25rem;
        margin-bottom: 15px;
    }
    
    .info-box-icon {
        border-top-left-radius: .25rem;
        border-bottom-left-radius: .25rem;
        display: block;
        float: left;
        height: 90px;
        width: 90px;
        text-align: center;
        font-size: 2rem;
        line-height: 90px;
        background: rgba(0,0,0,.2);
        color: rgba(255,255,255,.8);
    }
    
    .info-box-content {
        padding: 5px 10px;
        margin-left: 90px;
    }
    
    .info-box-number {
        display: block;
        font-weight: bold;
        font-size: 1.5rem;
    }
    
    .info-box-text {
        display: block;
        font-size: .875rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        text-transform: uppercase;
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    
    canvas {
        max-height: 300px;
    }
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Initialize charts
    initializeCharts();
    
    // Auto-generate report if initial type is set
    @if(isset($initialType) && $initialType)
    setTimeout(function() {
        try {
            const filters = {
                date_from: $('#date_from').val(),
                date_to: $('#date_to').val(),
                report_type: '{{ $initialType }}'
            };
            updateReports(filters);
        } catch (error) {
            console.error('Error auto-generating report:', error);
        }
    }, 1000);
    @endif
    
    // Apply filters button
    $('#applyFilters').click(function() {
        const filters = {
            date_from: $('#date_from').val(),
            date_to: $('#date_to').val(),
            report_type: $('#report_type').val()
        };
        
        updateReports(filters);
    });
    
    // Export report button
    $('#exportReport').click(function() {
        const filters = {
            date_from: $('#date_from').val(),
            date_to: $('#date_to').val(),
            report_type: $('#report_type').val()
        };
        
        exportReport('pdf', filters);
    });
});

function initializeCharts() {
    // Patient Demographics Chart (Pie Chart)
    const demographicsCtx = document.getElementById('patientDemographicsChart').getContext('2d');
    new Chart(demographicsCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($charts['demographics']['labels'] ?? ['Male', 'Female']) !!},
            datasets: [{
                data: {!! json_encode($charts['demographics']['data'] ?? [45, 55]) !!},
                backgroundColor: ['#36A2EB', '#FF6384', '#FFCE56', '#4BC0C0'],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    
    // Prescription Trends Chart (Line Chart)
    const trendsCtx = document.getElementById('prescriptionTrendsChart').getContext('2d');
    new Chart(trendsCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($charts['trends']['labels'] ?? []) !!},
            datasets: [{
                label: 'Prescriptions',
                data: {!! json_encode($charts['trends']['data'] ?? []) !!},
                borderColor: '#36A2EB',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // Top Medicines Chart (Bar Chart)
    const topMedicinesCtx = document.getElementById('topMedicinesChart').getContext('2d');
    new Chart(topMedicinesCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($charts['top_medicines']['labels'] ?? []) !!},
            datasets: [{
                label: 'Prescriptions Count',
                data: {!! json_encode($charts['top_medicines']['data'] ?? []) !!},
                backgroundColor: '#4BC0C0',
                borderColor: '#4BC0C0',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // Medicine Categories Chart (Doughnut Chart)
    const categoriesCtx = document.getElementById('medicineCategoriesChart').getContext('2d');
    new Chart(categoriesCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($charts['categories']['labels'] ?? []) !!},
            datasets: [{
                data: {!! json_encode($charts['categories']['data'] ?? []) !!},
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });
}

function updateReports(filters) {
    // Show loading state
    $('#reportsTable').html(`
        <div class="text-center py-5">
            <i class="fas fa-spinner fa-spin fa-3x text-primary mb-3"></i>
            <h5 class="text-muted">Loading report data...</h5>
        </div>
    `);
    
    // AJAX call to fetch report data
    $.ajax({
        url: '{{ route("reports.data") }}',
        method: 'GET',
        data: filters,
        success: function(response) {
            if (response.success) {
                updateStatsCards(response.stats);
                updateReportsTable(response.data, filters.report_type);
            } else {
                showError('Failed to load report data');
            }
        },
        error: function() {
            showError('Error loading report data');
        }
    });
}

function updateStatsCards(stats) {
    if (stats.patients) {
        $('#totalPatients').text(stats.patients.total || 0);
        $('#newPatientsMonth').text(stats.patients.new_this_month || 0);
    }
    
    if (stats.prescriptions) {
        $('#totalPrescriptions').text(stats.prescriptions.total || 0);
        $('#prescriptionsToday').text(stats.prescriptions.today || 0);
    }
    
    if (stats.medicines) {
        $('#totalMedicines').text(stats.medicines.total || 0);
        $('#lowStockAlerts').text(stats.medicines.low_stock || 0);
    }
    
    if (stats.financial) {
        $('#revenueMonth').text('₱' + (stats.financial.revenue_month || 0).toLocaleString());
    }
    
    if (stats.appointments) {
        $('#appointmentsToday').text(stats.appointments.today || 0);
    }
}

function updateReportsTable(data, reportType) {
    let tableHtml = '';
    
    switch (reportType) {
        case 'patients':
            tableHtml = generatePatientsTable(data);
            break;
        case 'prescriptions':
            tableHtml = generatePrescriptionsTable(data);
            break;
        case 'medicines':
            tableHtml = generateMedicinesTable(data);
            break;
        case 'financial':
            tableHtml = generateFinancialTable(data);
            break;
        default:
            tableHtml = generateOverviewTable(data);
    }
    
    $('#reportsTable').html(tableHtml);
    
    // Initialize DataTables if table exists
    if ($('#reportDataTable').length) {
        $('#reportDataTable').DataTable({
            responsive: true,
            pageLength: 25,
            order: [[0, 'desc']]
        });
    }
}

function generatePatientsTable(data) {
    if (!data || !data.length) return '<p class="text-center">No patient data available for the selected period.</p>';
    
    let html = `
        <table id="reportDataTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Patient Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Registration Date</th>
                    <th>Prescriptions Count</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    data.forEach(patient => {
        html += `
            <tr>
                <td>${patient.patient_name}</td>
                <td>${patient.email}</td>
                <td>${patient.phone || '-'}</td>
                <td>${patient.age || '-'}</td>
                <td>${patient.gender || '-'}</td>
                <td>${new Date(patient.created_at).toLocaleDateString()}</td>
                <td>${patient.prescriptions_count || 0}</td>
            </tr>
        `;
    });
    
    html += '</tbody></table>';
    return html;
}

function generatePrescriptionsTable(data) {
    if (!data || !data.length) return '<p class="text-center">No prescription data available for the selected period.</p>';
    
    let html = `
        <table id="reportDataTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Patient</th>
                    <th>Medicine</th>
                    <th>Dosage</th>
                    <th>Quantity</th>
                    <th>Status</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    data.forEach(prescription => {
        html += `
            <tr>
                <td>${new Date(prescription.prescribed_date).toLocaleDateString()}</td>
                <td>${prescription.patient_name}</td>
                <td>${prescription.medicine_name}</td>
                <td>${prescription.dosage}</td>
                <td>${prescription.quantity}</td>
                <td><span class="badge badge-${prescription.status === 'active' ? 'success' : 'primary'}">${prescription.status}</span></td>
                <td>₱${prescription.total_value ? prescription.total_value.toLocaleString() : '0.00'}</td>
            </tr>
        `;
    });
    
    html += '</tbody></table>';
    return html;
}

function generateMedicinesTable(data) {
    if (!data || !data.length) return '<p class="text-center">No medicine data available.</p>';
    
    let html = `
        <table id="reportDataTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Medicine Name</th>
                    <th>Category</th>
                    <th>Stock Quantity</th>
                    <th>Unit Price</th>
                    <th>Stock Value</th>
                    <th>Prescriptions</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    data.forEach(medicine => {
        const stockValue = (medicine.stock_quantity * medicine.unit_price) || 0;
        html += `
            <tr>
                <td>${medicine.medicine_name}</td>
                <td>${medicine.category.replace('_', ' ').toUpperCase()}</td>
                <td>${medicine.stock_quantity}</td>
                <td>₱${medicine.unit_price ? medicine.unit_price.toLocaleString() : '0.00'}</td>
                <td>₱${stockValue.toLocaleString()}</td>
                <td>${medicine.prescriptions_count || 0}</td>
                <td><span class="badge badge-${medicine.status === 'active' ? 'success' : 'secondary'}">${medicine.status}</span></td>
            </tr>
        `;
    });
    
    html += '</tbody></table>';
    return html;
}

function generateFinancialTable(data) {
    if (!data || !data.length) return '<p class="text-center">No financial data available for the selected period.</p>';
    
    let html = `
        <table id="reportDataTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Patient</th>
                    <th>Medicine</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    data.forEach(transaction => {
        html += `
            <tr>
                <td>${new Date(transaction.date).toLocaleDateString()}</td>
                <td>${transaction.patient_name}</td>
                <td>${transaction.medicine_name}</td>
                <td>${transaction.quantity}</td>
                <td>₱${transaction.unit_price ? transaction.unit_price.toLocaleString() : '0.00'}</td>
                <td>₱${transaction.total_amount ? transaction.total_amount.toLocaleString() : '0.00'}</td>
                <td><span class="badge badge-${transaction.status === 'active' ? 'success' : 'primary'}">${transaction.status}</span></td>
            </tr>
        `;
    });
    
    html += '</tbody></table>';
    return html;
}

function generateOverviewTable(data) {
    return `
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Overview Report:</strong> Use the filters above to select a specific report type (Patients, Prescriptions, Medicines, or Financial) to view detailed data.
                </div>
            </div>
        </div>
    `;
}

function exportReport(format, filters = null) {
    if (!filters) {
        filters = {
            date_from: $('#date_from').val(),
            date_to: $('#date_to').val(),
            report_type: $('#report_type').val()
        };
    }
    
    const params = new URLSearchParams({
        ...filters,
        format: format
    });
    
    window.open(`{{ route('reports.export') }}?${params}`, '_blank');
}

function showError(message) {
    $('#reportsTable').html(`
        <div class="alert alert-danger text-center">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            ${message}
        </div>
    `);
}
</script>
@endsection