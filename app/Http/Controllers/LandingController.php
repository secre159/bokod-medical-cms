<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Medicine;

class LandingController extends Controller
{
    /**
     * Show the landing page with system statistics
     */
    public function index()
    {
        // Get some basic statistics for display (if needed)
        $stats = [
            'total_patients' => Patient::count(),
            'total_appointments' => Appointment::count(),
            'total_medicines' => Medicine::count(),
            'total_users' => User::count(),
        ];
        
        return view('landing.index', compact('stats'));
    }
}
