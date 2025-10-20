<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Medicine;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display reports dashboard
     */
    public function index()
    {
        // Get summary statistics for reports
        $stats = [
            'total_patients' => Patient::count(),
            'total_appointments' => Appointment::count(),
            'total_medicines' => Medicine::count(),
            'low_stock_medicines' => Medicine::lowStock()->count(),
            'appointments_today' => Appointment::whereDate('appointment_date', today())->count(),
            'appointments_this_week' => Appointment::whereBetween('appointment_date', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'appointments_this_month' => Appointment::whereMonth('appointment_date', now()->month)->count(),
            'active_patients' => Patient::where('status', 'Active')->count()
        ];

        return view('reports.index', compact('stats'));
    }

    /**
     * Patient reports
     */
    public function patients(Request $request)
    {
        $query = Patient::query();
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }
        
        if ($request->filled('age_from')) {
            $query->whereRaw('DATEDIFF(CURDATE(), birth_date) / 365.25 >= ?', [$request->age_from]);
        }
        
        if ($request->filled('age_to')) {
            $query->whereRaw('DATEDIFF(CURDATE(), birth_date) / 365.25 <= ?', [$request->age_to]);
        }
        
        $patients = $query->withCount(['appointments', 'prescriptions'])->get();
        
        // Patient statistics by age group
        $ageGroups = Patient::selectRaw('
            CASE 
                WHEN DATEDIFF(CURDATE(), birth_date) / 365.25 < 18 THEN "Under 18"
                WHEN DATEDIFF(CURDATE(), birth_date) / 365.25 BETWEEN 18 AND 30 THEN "18-30"
                WHEN DATEDIFF(CURDATE(), birth_date) / 365.25 BETWEEN 31 AND 50 THEN "31-50"
                WHEN DATEDIFF(CURDATE(), birth_date) / 365.25 BETWEEN 51 AND 65 THEN "51-65"
                ELSE "Over 65"
            END as age_group,
            COUNT(*) as count
        ')
        ->groupBy('age_group')
        ->get();
        
        // Gender distribution
        $genderStats = Patient::select('gender', DB::raw('COUNT(*) as count'))
            ->groupBy('gender')
            ->get();
            
        return view('reports.patients', compact('patients', 'ageGroups', 'genderStats'));
    }

    /**
     * Appointment reports
     */
    public function appointments(Request $request)
    {
        $query = Appointment::with(['patient', 'user']);
        
        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('appointment_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('appointment_date', '<=', $request->date_to);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        $appointments = $query->orderBy('appointment_date', 'desc')->get();
        
        // Monthly appointment trends
        $monthlyStats = Appointment::selectRaw('
            YEAR(appointment_date) as year,
            MONTH(appointment_date) as month,
            COUNT(*) as total_appointments,
            SUM(CASE WHEN status = "Completed" THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN status = "Cancelled" THEN 1 ELSE 0 END) as cancelled
        ')
        ->where('appointment_date', '>=', now()->subMonths(12))
        ->groupBy('year', 'month')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->get();
        
        // Status distribution
        $statusStats = Appointment::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();
            
        return view('reports.appointments', compact('appointments', 'monthlyStats', 'statusStats'));
    }

    /**
     * Medicine inventory reports
     */
    public function medicines(Request $request)
    {
        $query = Medicine::query();
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('dosage_form')) {
            $query->where('dosage_form', $request->dosage_form);
        }
        
        if ($request->boolean('low_stock_only')) {
            $query->lowStock();
        }
        
        if ($request->boolean('expiring_soon')) {
            $query->where('expiry_date', '<=', now()->addMonths(3));
        }
        
        $medicines = $query->withCount('prescriptions')->get();
        
        // Stock value calculation
        $totalValue = Medicine::sum(DB::raw('stock_quantity * unit_price'));
        $lowStockValue = Medicine::lowStock()->sum(DB::raw('stock_quantity * unit_price'));
        
        // Expiring medicines
        $expiringMedicines = Medicine::where('expiry_date', '<=', now()->addMonths(3))
            ->where('expiry_date', '>=', now())
            ->orderBy('expiry_date')
            ->get();
            
        return view('reports.medicines', compact('medicines', 'totalValue', 'lowStockValue', 'expiringMedicines'));
    }

    /**
     * User activity reports
     */
    public function users(Request $request)
    {
        $users = User::with(['appointments', 'prescriptions'])
            ->withCount(['appointments', 'prescriptions'])
            ->get();
            
        // User role distribution
        $roleStats = User::select('role', DB::raw('COUNT(*) as count'))
            ->groupBy('role')
            ->get();
            
        return view('reports.users', compact('users', 'roleStats'));
    }

    /**
     * Export report as PDF
     */
    public function exportPdf(Request $request, $type)
    {
        // This would implement PDF generation using Laravel PDF
        // For now, return a simple response
        return response()->json([
            'message' => 'PDF export functionality will be implemented with dompdf package',
            'type' => $type
        ]);
    }

    /**
     * Export report as Excel
     */
    public function exportExcel(Request $request, $type)
    {
        // This would implement Excel generation using Laravel Excel
        return response()->json([
            'message' => 'Excel export functionality will be implemented with Laravel Excel package',
            'type' => $type
        ]);
    }
}
