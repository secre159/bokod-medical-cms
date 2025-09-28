<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminDocumentationController extends Controller
{
    /**
     * Display the documentation index page
     */
    public function index()
    {
        $sections = [
            [
                'title' => 'System Overview',
                'description' => 'General overview of the CMS system and its capabilities',
                'icon' => 'fas fa-info-circle',
                'route' => 'admin.documentation.system-overview'
            ],
            [
                'title' => 'User Management',
                'description' => 'Managing users, roles, and permissions in the system',
                'icon' => 'fas fa-users-cog',
                'route' => 'admin.documentation.user-management'
            ],
            [
                'title' => 'Patient Management',
                'description' => 'Comprehensive guide to patient registration and management',
                'icon' => 'fas fa-user-injured',
                'route' => 'admin.documentation.patient-management'
            ],
            [
                'title' => 'Appointment Management',
                'description' => 'Scheduling and managing patient appointments',
                'icon' => 'fas fa-calendar-check',
                'route' => 'admin.documentation.appointment-management'
            ],
            [
                'title' => 'Medicine & Prescription Management',
                'description' => 'Managing medicines, stock, and prescriptions',
                'icon' => 'fas fa-pills',
                'route' => 'admin.documentation.medicine-management'
            ],
            [
                'title' => 'Messaging System',
                'description' => 'Communication features between admins and patients',
                'icon' => 'fas fa-comments',
                'route' => 'admin.documentation.messaging-system'
            ],
            [
                'title' => 'Reports & Analytics',
                'description' => 'Generating and interpreting system reports',
                'icon' => 'fas fa-chart-bar',
                'route' => 'admin.documentation.reports-analytics'
            ],
            [
                'title' => 'Technical Documentation',
                'description' => 'Technical details, API references, and system architecture',
                'icon' => 'fas fa-code',
                'route' => 'admin.documentation.technical'
            ]
        ];

        return view('admin.documentation.index', compact('sections'));
    }

    /**
     * Display system overview documentation
     */
    public function systemOverview()
    {
        return view('admin.documentation.system-overview');
    }

    /**
     * Display user management documentation
     */
    public function userManagement()
    {
        return view('admin.documentation.user-management');
    }

    /**
     * Display patient management documentation
     */
    public function patientManagement()
    {
        return view('admin.documentation.patient-management');
    }

    /**
     * Display appointment management documentation
     */
    public function appointmentManagement()
    {
        return view('admin.documentation.appointment-management');
    }

    /**
     * Display medicine management documentation
     */
    public function medicineManagement()
    {
        return view('admin.documentation.medicine-management');
    }

    /**
     * Display messaging system documentation
     */
    public function messagingSystem()
    {
        return view('admin.documentation.messaging-system');
    }

    /**
     * Display reports and analytics documentation
     */
    public function reportsAnalytics()
    {
        return view('admin.documentation.reports-analytics');
    }

    /**
     * Display technical documentation
     */
    public function technical()
    {
        return view('admin.documentation.technical');
    }
}