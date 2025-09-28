@extends('adminlte::page')

@section('title', 'Reports & Analytics - Documentation')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-chart-bar"></i> Reports & Analytics
            <small class="text-muted">Complete step-by-step reporting and analytics guide</small>
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
                        <a class="nav-link" href="#accessing-reports">Accessing Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#patient-reports">Patient Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#appointment-reports">Appointment Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#medicine-reports">Medicine Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#financial-reports">Financial Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#custom-reports">Custom Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#analytics-dashboard">Analytics Dashboard</a>
                    </li>
                </ul>
            </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-9">
        <!-- Accessing Reports Section -->
        <div class="card" id="accessing-reports">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-file-alt"></i> Accessing Reports - Step by Step</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> <strong>About Reports:</strong> 
                    The reporting system provides comprehensive insights into your healthcare operations, helping you make data-driven decisions.
                </div>

                <h5><i class="fas fa-step-forward text-primary"></i> How to Access Reports</h5>
                <div class="card border-primary mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Navigate to Reports:</strong> Click "Reports" in the left sidebar</li>
                            <li><strong>Reports Dashboard Opens:</strong> Shows available report categories</li>
                            <li><strong>Quick Access:</strong> Recent reports and favorites appear first</li>
                        </ol>
                        
                        <p><strong>Report Categories Available:</strong></p>
                        <ul>
                            <li><strong>Patient Reports:</strong> Patient demographics and activity</li>
                            <li><strong>Appointment Reports:</strong> Scheduling and utilization data</li>
                            <li><strong>Medicine Reports:</strong> Inventory and prescription analytics</li>
                            <li><strong>Financial Reports:</strong> Revenue and cost analysis</li>
                            <li><strong>Operational Reports:</strong> System usage and performance</li>
                        </ul>
                    </div>
                </div>

                <h5><i class="fas fa-step-forward text-success"></i> General Report Generation Process</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Select Report Type:</strong> Choose from available categories</li>
                            <li><strong>Set Parameters:</strong>
                                <ul>
                                    <li>Date range (start and end dates)</li>
                                    <li>Filtering criteria (patient type, status, etc.)</li>
                                    <li>Grouping options (by day, week, month)</li>
                                </ul>
                            </li>
                            <li><strong>Preview Report:</strong> Review data before final generation</li>
                            <li><strong>Generate Report:</strong> Click "Generate" button</li>
                            <li><strong>Export Options:</strong> PDF, Excel, CSV, or print</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Patient Reports Section -->
        <div class="card" id="patient-reports">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-users"></i> Patient Reports</h3>
            </div>
            <div class="card-body">
                <h5><i class="fas fa-chart-pie text-primary"></i> Patient Demographics Report</h5>
                <div class="card border-primary mb-3">
                    <div class="card-body">
                        <p><strong>How to Generate:</strong></p>
                        <ol>
                            <li><strong>Go to Reports → Patient Reports</strong></li>
                            <li><strong>Select "Demographics Report"</strong></li>
                            <li><strong>Choose Parameters:</strong>
                                <ul>
                                    <li>Registration date range</li>
                                    <li>Age groups to include</li>
                                    <li>Gender breakdown</li>
                                    <li>Geographic location filters</li>
                                </ul>
                            </li>
                            <li><strong>Generate Report</strong></li>
                        </ol>
                        
                        <p><strong>Information Included:</strong></p>
                        <ul>
                            <li>Total patient count</li>
                            <li>Age distribution charts</li>
                            <li>Gender breakdown</li>
                            <li>New patient registrations over time</li>
                            <li>Patient status summary</li>
                        </ul>
                    </div>
                </div>

                <h5><i class="fas fa-activity text-success"></i> Patient Activity Report</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <p><strong>Track Patient Engagement:</strong></p>
                        <ul>
                            <li><strong>Visit Frequency:</strong> How often patients visit</li>
                            <li><strong>Appointment History:</strong> Completed vs. missed appointments</li>
                            <li><strong>Portal Usage:</strong> Patient portal login activity</li>
                            <li><strong>Communication:</strong> Message activity with staff</li>
                        </ul>
                        
                        <p><strong>Steps to Generate:</strong></p>
                        <ol>
                            <li>Select "Patient Activity Report"</li>
                            <li>Set time period for analysis</li>
                            <li>Choose specific patients or all patients</li>
                            <li>Select activity types to include</li>
                            <li>Generate and export report</li>
                        </ol>
                    </div>
                </div>

                <h5><i class="fas fa-heartbeat text-info"></i> Health Outcomes Report</h5>
                <div class="card border-info mb-3">
                    <div class="card-body">
                        <p><strong>Analyze Patient Health Trends:</strong></p>
                        <ul>
                            <li>Common diagnoses and conditions</li>
                            <li>Treatment effectiveness tracking</li>
                            <li>Prescription adherence rates</li>
                            <li>Follow-up completion rates</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointment Reports Section -->
        <div class="card" id="appointment-reports">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-calendar-check"></i> Appointment Reports</h3>
            </div>
            <div class="card-body">
                <h5><i class="fas fa-clock text-primary"></i> Appointment Utilization Report</h5>
                <div class="card border-primary mb-3">
                    <div class="card-body">
                        <p><strong>Measure Scheduling Efficiency:</strong></p>
                        <ol>
                            <li><strong>Access Report:</strong> Reports → Appointment Reports → Utilization</li>
                            <li><strong>Set Parameters:</strong>
                                <ul>
                                    <li>Date range to analyze</li>
                                    <li>Specific days of week</li>
                                    <li>Time slots to include</li>
                                    <li>Provider or location filters</li>
                                </ul>
                            </li>
                            <li><strong>Review Metrics:</strong>
                                <ul>
                                    <li>Total appointments scheduled</li>
                                    <li>Appointment completion rate</li>
                                    <li>No-show percentage</li>
                                    <li>Cancellation rates</li>
                                    <li>Peak scheduling times</li>
                                </ul>
                            </li>
                        </ol>
                    </div>
                </div>

                <h5><i class="fas fa-chart-line text-success"></i> Appointment Trends Report</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <p><strong>Analyze Scheduling Patterns:</strong></p>
                        <ul>
                            <li><strong>Seasonal Trends:</strong> Busy vs. slow periods</li>
                            <li><strong>Day-of-Week Patterns:</strong> Most popular appointment days</li>
                            <li><strong>Time Preferences:</strong> Popular appointment times</li>
                            <li><strong>Appointment Types:</strong> Most requested services</li>
                        </ul>
                        
                        <p><strong>How to Use This Data:</strong></p>
                        <ul>
                            <li>Optimize staff scheduling</li>
                            <li>Adjust operating hours</li>
                            <li>Plan for seasonal variations</li>
                            <li>Improve resource allocation</li>
                        </ul>
                    </div>
                </div>

                <h5><i class="fas fa-user-clock text-warning"></i> No-Show Analysis Report</h5>
                <div class="card border-warning mb-3">
                    <div class="card-body">
                        <p><strong>Identify No-Show Patterns:</strong></p>
                        <ol>
                            <li>Generate no-show report for selected period</li>
                            <li>Review patient-specific no-show rates</li>
                            <li>Identify time slots with highest no-shows</li>
                            <li>Analyze reasons for missed appointments</li>
                            <li>Develop strategies to reduce no-shows</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Medicine Reports Section -->
        <div class="card" id="medicine-reports">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-pills"></i> Medicine Reports</h3>
            </div>
            <div class="card-body">
                <h5><i class="fas fa-boxes text-primary"></i> Inventory Report</h5>
                <div class="card border-primary mb-3">
                    <div class="card-body">
                        <p><strong>Monitor Stock Levels:</strong></p>
                        <ol>
                            <li><strong>Access:</strong> Reports → Medicine Reports → Inventory</li>
                            <li><strong>View Data:</strong>
                                <ul>
                                    <li>Current stock levels for all medicines</li>
                                    <li>Low stock alerts</li>
                                    <li>Expiring medicines</li>
                                    <li>Stock value calculations</li>
                                </ul>
                            </li>
                            <li><strong>Export Options:</strong> Excel for inventory management</li>
                        </ol>
                    </div>
                </div>

                <h5><i class="fas fa-prescription-bottle-alt text-success"></i> Prescription Analysis Report</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <p><strong>Analyze Prescribing Patterns:</strong></p>
                        <ul>
                            <li><strong>Top Prescribed Medicines:</strong> Most frequently prescribed</li>
                            <li><strong>Prescribing Trends:</strong> Changes over time</li>
                            <li><strong>Patient Compliance:</strong> Prescription pickup rates</li>
                            <li><strong>Cost Analysis:</strong> Prescription costs and savings</li>
                        </ul>
                        
                        <p><strong>Steps to Generate:</strong></p>
                        <ol>
                            <li>Select date range for analysis</li>
                            <li>Choose specific medicines or all medicines</li>
                            <li>Filter by patient demographics if needed</li>
                            <li>Generate comprehensive prescription report</li>
                        </ol>
                    </div>
                </div>

                <h5><i class="fas fa-exclamation-triangle text-warning"></i> Stock Alert Report</h5>
                <div class="card border-warning mb-3">
                    <div class="card-body">
                        <p><strong>Proactive Stock Management:</strong></p>
                        <ul>
                            <li>Medicines below minimum threshold</li>
                            <li>Items approaching expiration</li>
                            <li>Fast-moving items needing reorder</li>
                            <li>Slow-moving inventory for review</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Reports Section -->
        <div class="card" id="financial-reports">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-dollar-sign"></i> Financial Reports</h3>
            </div>
            <div class="card-body">
                <h5><i class="fas fa-chart-area text-primary"></i> Revenue Analysis Report</h5>
                <div class="card border-primary mb-3">
                    <div class="card-body">
                        <p><strong>Track Financial Performance:</strong></p>
                        <ol>
                            <li><strong>Access:</strong> Reports → Financial Reports → Revenue</li>
                            <li><strong>Analyze Revenue Streams:</strong>
                                <ul>
                                    <li>Appointment fees</li>
                                    <li>Medicine sales</li>
                                    <li>Treatment costs</li>
                                    <li>Insurance reimbursements</li>
                                </ul>
                            </li>
                            <li><strong>Time-based Analysis:</strong>
                                <ul>
                                    <li>Monthly revenue trends</li>
                                    <li>Seasonal patterns</li>
                                    <li>Year-over-year comparisons</li>
                                </ul>
                            </li>
                        </ol>
                    </div>
                </div>

                <h5><i class="fas fa-calculator text-success"></i> Cost Analysis Report</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <p><strong>Monitor Operating Expenses:</strong></p>
                        <ul>
                            <li><strong>Medicine Costs:</strong> Inventory and purchasing expenses</li>
                            <li><strong>Operational Costs:</strong> System maintenance and overhead</li>
                            <li><strong>Efficiency Metrics:</strong> Cost per patient, cost per appointment</li>
                        </ul>
                    </div>
                </div>

                <h5><i class="fas fa-balance-scale text-info"></i> Profit & Loss Report</h5>
                <div class="card border-info mb-3">
                    <div class="card-body">
                        <p><strong>Comprehensive Financial Overview:</strong></p>
                        <ol>
                            <li>Total revenue by category</li>
                            <li>Operating expenses breakdown</li>
                            <li>Net profit calculations</li>
                            <li>Margin analysis</li>
                            <li>Financial health indicators</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Custom Reports Section -->
        <div class="card" id="custom-reports">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-cog"></i> Custom Reports</h3>
            </div>
            <div class="card-body">
                <h5><i class="fas fa-step-forward text-primary"></i> Creating Custom Reports</h5>
                <div class="card border-primary mb-3">
                    <div class="card-body">
                        <ol>
                            <li><strong>Access Report Builder:</strong> Reports → Custom Reports → Create New</li>
                            <li><strong>Select Data Sources:</strong>
                                <ul>
                                    <li>Patient data</li>
                                    <li>Appointment information</li>
                                    <li>Medicine data</li>
                                    <li>Financial records</li>
                                </ul>
                            </li>
                            <li><strong>Choose Fields:</strong> Select specific data points to include</li>
                            <li><strong>Set Filters:</strong> Define criteria for data inclusion</li>
                            <li><strong>Configure Grouping:</strong> How to organize the data</li>
                            <li><strong>Add Calculations:</strong> Sums, averages, percentages</li>
                            <li><strong>Preview & Save:</strong> Test report and save for future use</li>
                        </ol>
                    </div>
                </div>

                <h5><i class="fas fa-save text-success"></i> Managing Saved Reports</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <p><strong>Report Management Features:</strong></p>
                        <ul>
                            <li><strong>Save Favorite Reports:</strong> Quick access to frequently used reports</li>
                            <li><strong>Schedule Reports:</strong> Automatic generation and delivery</li>
                            <li><strong>Share Reports:</strong> Send to team members or stakeholders</li>
                            <li><strong>Version Control:</strong> Track report changes and updates</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analytics Dashboard Section -->
        <div class="card" id="analytics-dashboard">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-tachometer-alt"></i> Analytics Dashboard</h3>
            </div>
            <div class="card-body">
                <h5><i class="fas fa-chart-line text-primary"></i> Key Performance Indicators (KPIs)</h5>
                <div class="card border-primary mb-3">
                    <div class="card-body">
                        <p><strong>Essential Metrics to Monitor:</strong></p>
                        <ul>
                            <li><strong>Patient Metrics:</strong>
                                <ul>
                                    <li>Total active patients</li>
                                    <li>New patient acquisition rate</li>
                                    <li>Patient retention rate</li>
                                    <li>Patient satisfaction scores</li>
                                </ul>
                            </li>
                            <li><strong>Operational Metrics:</strong>
                                <ul>
                                    <li>Appointment utilization rate</li>
                                    <li>Average wait time</li>
                                    <li>No-show rate</li>
                                    <li>System uptime</li>
                                </ul>
                            </li>
                            <li><strong>Financial Metrics:</strong>
                                <ul>
                                    <li>Monthly revenue</li>
                                    <li>Revenue per patient</li>
                                    <li>Cost per appointment</li>
                                    <li>Profit margins</li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>

                <h5><i class="fas fa-eye text-success"></i> Using the Analytics Dashboard</h5>
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <p><strong>Dashboard Features:</strong></p>
                        <ol>
                            <li><strong>Real-time Updates:</strong> Live data refreshing</li>
                            <li><strong>Interactive Charts:</strong> Click to drill down into details</li>
                            <li><strong>Customizable Views:</strong> Arrange widgets as needed</li>
                            <li><strong>Export Options:</strong> Share dashboard screenshots or data</li>
                        </ol>
                        
                        <p><strong>How to Customize Your Dashboard:</strong></p>
                        <ol>
                            <li>Click "Customize Dashboard" button</li>
                            <li>Select KPIs most relevant to your practice</li>
                            <li>Arrange widgets in preferred layout</li>
                            <li>Set refresh intervals for data updates</li>
                            <li>Save your personalized dashboard configuration</li>
                        </ol>
                    </div>
                </div>

                <h5><i class="fas fa-lightbulb text-warning"></i> Making Data-Driven Decisions</h5>
                <div class="card border-warning mb-3">
                    <div class="card-body">
                        <p><strong>How to Use Analytics for Improvement:</strong></p>
                        <ul>
                            <li><strong>Identify Trends:</strong> Look for patterns in patient behavior</li>
                            <li><strong>Spot Problems Early:</strong> Use alerts for declining metrics</li>
                            <li><strong>Optimize Resources:</strong> Adjust staffing based on busy periods</li>
                            <li><strong>Improve Patient Care:</strong> Focus on areas with lower satisfaction</li>
                            <li><strong>Financial Planning:</strong> Use revenue trends for budgeting</li>
                        </ul>
                        
                        <p><strong>Regular Review Schedule:</strong></p>
                        <ul>
                            <li><strong>Daily:</strong> Check operational KPIs</li>
                            <li><strong>Weekly:</strong> Review patient and appointment trends</li>
                            <li><strong>Monthly:</strong> Analyze financial performance</li>
                            <li><strong>Quarterly:</strong> Comprehensive business review</li>
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
