<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Medicine;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportsController extends Controller
{
    /**
     * Display the reports dashboard
     */
    public function dashboard()
    {
        $stats = $this->getStatistics();
        $charts = $this->getChartsData();
        
        return view('reports.dashboard', compact('stats', 'charts'));
    }
    
    /**
     * Display patients report
     */
    public function patients()
    {
        $stats = $this->getStatistics();
        $charts = $this->getChartsData();
        $initialType = 'patients';
        
        return view('reports.dashboard', compact('stats', 'charts', 'initialType'));
    }
    
    /**
     * Display prescriptions report
     */
    public function prescriptions()
    {
        $stats = $this->getStatistics();
        $charts = $this->getChartsData();
        $initialType = 'prescriptions';
        
        return view('reports.dashboard', compact('stats', 'charts', 'initialType'));
    }
    
    /**
     * Display medicines report
     */
    public function medicines()
    {
        $stats = $this->getStatistics();
        $charts = $this->getChartsData();
        $initialType = 'medicines';
        
        return view('reports.dashboard', compact('stats', 'charts', 'initialType'));
    }
    
    /**
     * Display visits report (alias for appointments)
     */
    public function visits()
    {
        $stats = $this->getStatistics();
        $charts = $this->getChartsData();
        $initialType = 'patients';
        
        return view('reports.dashboard', compact('stats', 'charts', 'initialType'));
    }
    
    /**
     * Get report data via AJAX
     */
    public function getData(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth());
        $dateTo = $request->input('date_to', now());
        $reportType = $request->input('report_type', 'overview');
        
        $stats = $this->getStatistics($dateFrom, $dateTo);
        $data = $this->getReportData($reportType, $dateFrom, $dateTo);
        
        return response()->json([
            'success' => true,
            'stats' => $stats,
            'data' => $data
        ]);
    }
    
    /**
     * Export reports in different formats
     */
    public function export(Request $request)
    {
        $format = $request->input('format', 'pdf');
        $dateFrom = $request->input('date_from', now()->startOfMonth());
        $dateTo = $request->input('date_to', now());
        $reportType = $request->input('report_type', 'overview');
        
        $data = $this->getReportData($reportType, $dateFrom, $dateTo);
        $stats = $this->getStatistics($dateFrom, $dateTo);
        
        switch ($format) {
            case 'pdf':
                return $this->exportPDF($reportType, $data, $stats, $dateFrom, $dateTo);
            case 'excel':
                return $this->exportExcel($reportType, $data, $stats, $dateFrom, $dateTo);
            case 'csv':
                return $this->exportCSV($reportType, $data, $stats, $dateFrom, $dateTo);
            default:
                return response()->json(['error' => 'Invalid format'], 400);
        }
    }
    
    /**
     * Get overall statistics
     */
    private function getStatistics($dateFrom = null, $dateTo = null)
    {
        $dateFrom = $dateFrom ? Carbon::parse($dateFrom) : now()->startOfMonth();
        $dateTo = $dateTo ? Carbon::parse($dateTo) : now();
        
        return [
            'patients' => [
                'total' => Patient::count(),
                'new_this_month' => Patient::whereBetween('created_at', [$dateFrom, $dateTo])->count(),
                'active' => Patient::where('archived', false)->count(),
                'by_gender' => Patient::select('gender', DB::raw('count(*) as count'))
                    ->groupBy('gender')
                    ->pluck('count', 'gender')
                    ->toArray()
            ],
            'appointments' => [
                'total' => Appointment::whereBetween('appointment_date', [$dateFrom, $dateTo])->count(),
                'today' => Appointment::whereDate('appointment_date', now())->count(),
                'pending' => Appointment::where('approval_status', 'pending')->count(),
                'approved' => Appointment::where('approval_status', 'approved')->count(),
                'completed' => Appointment::where('status', 'completed')->count()
            ],
            'prescriptions' => [
                'total' => Prescription::whereBetween('prescribed_date', [$dateFrom, $dateTo])->count(),
                'today' => Prescription::whereDate('prescribed_date', now())->count(),
                'dispensed' => Prescription::where('status', 'completed')->count(),
                'active' => Prescription::where('status', 'active')->count()
            ],
            'medicines' => [
                'total' => Medicine::count(),
                'active' => Medicine::where('status', 'active')->count(),
                'low_stock' => Medicine::whereRaw('stock_quantity <= minimum_stock')->count(),
                'out_of_stock' => Medicine::where('stock_quantity', 0)->count(),
                'categories' => Medicine::select('category', DB::raw('count(*) as count'))
                    ->groupBy('category')
                    ->pluck('count', 'category')
                    ->toArray()
            ],
            'financial' => [
                'revenue_month' => 0, // TODO: Implement when pricing system is added
                'revenue_today' => 0 // TODO: Implement when pricing system is added
            ]
        ];
    }
    
    /**
     * Get charts data
     */
    private function getChartsData()
    {
        return [
            'demographics' => [
                'labels' => ['Male', 'Female', 'Other'],
                'data' => [
                    Patient::where('gender', 'male')->count(),
                    Patient::where('gender', 'female')->count(),
                    Patient::whereNotIn('gender', ['male', 'female'])->count()
                ]
            ],
            'trends' => [
                'labels' => $this->getLast7Days(),
                'data' => $this->getPrescriptionTrendsData()
            ],
            'top_medicines' => [
                'labels' => $this->getTopMedicines()->pluck('medicine_name')->toArray(),
                'data' => $this->getTopMedicines()->pluck('prescriptions_count')->toArray()
            ],
            'categories' => [
                'labels' => array_keys(Medicine::select('category', DB::raw('count(*) as count'))
                    ->groupBy('category')
                    ->pluck('count', 'category')
                    ->toArray()),
                'data' => array_values(Medicine::select('category', DB::raw('count(*) as count'))
                    ->groupBy('category')
                    ->pluck('count', 'category')
                    ->toArray())
            ]
        ];
    }
    
    /**
     * Get specific report data based on type
     */
    private function getReportData($type, $dateFrom, $dateTo)
    {
        switch ($type) {
            case 'patients':
                return $this->getPatientsReportData($dateFrom, $dateTo);
            case 'prescriptions':
                return $this->getPrescriptionsReportData($dateFrom, $dateTo);
            case 'medicines':
                return $this->getMedicinesReportData();
            case 'financial':
                return $this->getFinancialReportData($dateFrom, $dateTo);
            default:
                return [];
        }
    }
    
    /**
     * Get patients report data
     */
    private function getPatientsReportData($dateFrom, $dateTo)
    {
        return Patient::with(['prescriptions'])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->get()
            ->map(function ($patient) {
                return [
                    'patient_name' => $patient->patient_name,
                    'email' => $patient->email,
                    'phone' => $patient->phone_number,
                    'age' => $patient->age,
                    'gender' => $patient->gender,
                    'created_at' => $patient->created_at,
                    'prescriptions_count' => $patient->prescriptions->count()
                ];
            });
    }
    
    /**
     * Get prescriptions report data
     */
    private function getPrescriptionsReportData($dateFrom, $dateTo)
    {
        return Prescription::with(['patient', 'medicine'])
            ->whereBetween('prescribed_date', [$dateFrom, $dateTo])
            ->get()
            ->map(function ($prescription) {
                return [
                    'prescribed_date' => $prescription->prescribed_date,
                    'patient_name' => $prescription->patient->patient_name ?? 'N/A',
                    'medicine_name' => $prescription->medicine->medicine_name ?? 'N/A',
                    'dosage' => $prescription->dosage,
                    'quantity' => $prescription->quantity,
                    'status' => $prescription->status,
                    'total_value' => 'N/A' // TODO: Calculate when pricing system is implemented
                ];
            });
    }
    
    /**
     * Get medicines report data
     */
    private function getMedicinesReportData()
    {
        return Medicine::withCount('prescriptions')
            ->get()
            ->map(function ($medicine) {
                return [
                    'medicine_name' => $medicine->medicine_name,
                    'category' => $medicine->category,
                    'stock_quantity' => $medicine->stock_quantity,
                    'unit_price' => $medicine->unit_price,
                    'prescriptions_count' => $medicine->prescriptions_count,
                    'status' => $medicine->status
                ];
            });
    }
    
    /**
     * Get financial report data
     */
    private function getFinancialReportData($dateFrom, $dateTo)
    {
        return Prescription::with(['patient', 'medicine'])
            ->whereBetween('prescribed_date', [$dateFrom, $dateTo])
            ->get()
            ->map(function ($prescription) {
                return [
                    'date' => $prescription->prescribed_date,
                    'patient_name' => $prescription->patient->patient_name ?? 'N/A',
                    'medicine_name' => $prescription->medicine->medicine_name ?? 'N/A',
                    'quantity' => $prescription->quantity ?? 0,
                    'unit_price' => 0, // TODO: Implement when pricing system is added
                    'total_amount' => 0, // TODO: Implement when pricing system is added
                    'status' => $prescription->status ?? 'active'
                ];
            });
    }
    
    /**
     * Get last 7 days for trends
     */
    private function getLast7Days()
    {
        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $days[] = now()->subDays($i)->format('M d');
        }
        return $days;
    }
    
    /**
     * Get prescription trends data for last 7 days
     */
    private function getPrescriptionTrendsData()
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $count = Prescription::whereDate('prescribed_date', $date)->count();
            $data[] = $count;
        }
        return $data;
    }
    
    /**
     * Get top 5 prescribed medicines
     */
    private function getTopMedicines()
    {
        return Medicine::withCount('prescriptions')
            ->orderBy('prescriptions_count', 'desc')
            ->limit(5)
            ->get();
    }
    
    /**
     * Export to PDF
     */
    private function exportPDF($type, $data, $stats, $dateFrom, $dateTo)
    {
        $pdf = Pdf::loadView('reports.export.pdf', [
            'type' => $type,
            'data' => $data,
            'stats' => $stats,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo
        ]);
        
        $pdf->setPaper('A4', 'portrait');
        
        $filename = "report_{$type}_" . now()->format('Y_m_d_H_i_s') . '.pdf';
        
        return $pdf->download($filename);
    }
    
    /**
     * Export to Excel
     */
    private function exportExcel($type, $data, $stats, $dateFrom, $dateTo)
    {
        $filename = "report_{$type}_" . now()->format('Y_m_d_H_i_s') . '.xlsx';
        
        // For now, return CSV format - can be enhanced with proper Excel library
        return $this->exportCSV($type, $data, $stats, $dateFrom, $dateTo, 'xlsx');
    }
    
    /**
     * Export to CSV
     */
    private function exportCSV($type, $data, $stats, $dateFrom, $dateTo, $format = 'csv')
    {
        $filename = "report_{$type}_" . now()->format('Y_m_d_H_i_s') . ".{$format}";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];
        
        $callback = function() use ($type, $data) {
            $file = fopen('php://output', 'w');
            
            // Add headers based on report type
            switch ($type) {
                case 'patients':
                    fputcsv($file, ['Patient Name', 'Email', 'Phone', 'Age', 'Gender', 'Registration Date', 'Prescriptions Count']);
                    break;
                case 'prescriptions':
                    fputcsv($file, ['Date', 'Patient', 'Medicine', 'Dosage', 'Quantity', 'Status', 'Total Value']);
                    break;
                case 'medicines':
                    fputcsv($file, ['Medicine Name', 'Category', 'Stock Quantity', 'Unit Price', 'Prescriptions Count', 'Status']);
                    break;
                case 'financial':
                    fputcsv($file, ['Date', 'Patient', 'Medicine', 'Quantity', 'Unit Price', 'Total Amount', 'Status']);
                    break;
            }
            
            // Add data rows
            foreach ($data as $row) {
                fputcsv($file, array_values($row));
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}