<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use League\CommonMark\CommonMarkConverter;

class DocumentationController extends Controller
{
    private $converter;

    public function __construct()
    {
        $this->converter = new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }

    /**
     * Display the documentation index page
     */
    public function index()
    {
        return view('documentation.index');
    }

    /**
     * Display the complete admin usage guide
     */
    public function adminGuide()
    {
        try {
            $markdownPath = base_path('ADMIN_USAGE_GUIDE.md');
            
            if (!File::exists($markdownPath)) {
                abort(404, 'Admin Usage Guide not found');
            }
            
            $markdownContent = File::get($markdownPath);
            $htmlContent = $this->converter->convert($markdownContent);
            
            return view('documentation.admin-guide', [
                'title' => 'Admin Usage Guide',
                'content' => $htmlContent,
                'lastUpdated' => File::lastModified($markdownPath)
            ]);
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to load admin guide: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the quick reference guide
     */
    public function quickGuide()
    {
        try {
            $markdownPath = base_path('ADMIN_QUICK_GUIDE.md');
            
            if (!File::exists($markdownPath)) {
                abort(404, 'Quick Guide not found');
            }
            
            $markdownContent = File::get($markdownPath);
            $htmlContent = $this->converter->convert($markdownContent);
            
            return view('documentation.quick-guide', [
                'title' => 'Quick Reference Guide',
                'content' => $htmlContent,
                'lastUpdated' => File::lastModified($markdownPath)
            ]);
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to load quick guide: ' . $e->getMessage()]);
        }
    }

    /**
     * Display system help for specific modules
     */
    public function moduleHelp($module)
    {
        $helpContent = $this->getModuleHelpContent($module);
        
        if (!$helpContent) {
            abort(404, 'Help content not found for this module');
        }
        
        return view('documentation.module-help', [
            'module' => ucfirst($module),
            'content' => $helpContent
        ]);
    }

    /**
     * Get help content for specific modules
     */
    private function getModuleHelpContent($module)
    {
        $helpContent = [
            'dashboard' => [
                'title' => 'Dashboard Help',
                'sections' => [
                    'Overview' => 'The dashboard provides a quick overview of your system with real-time statistics and alerts.',
                    'Quick Actions' => 'Use the quick action buttons to rapidly access common tasks like adding patients or medicines.',
                    'Statistics Cards' => 'Monitor key metrics including patient count, appointments, prescriptions, and medicine alerts.',
                    'Activity Feed' => 'View recent system activity and important notifications.'
                ]
            ],
            'patients' => [
                'title' => 'Patient Management Help',
                'sections' => [
                    'Patient List' => 'View all registered patients with search and filter capabilities.',
                    'Adding Patients' => 'Click "Add New Patient" to register new patients with complete medical information.',
                    'Patient Profiles' => 'Access detailed patient information including medical history, appointments, and prescriptions.',
                    'Medical Records' => 'Maintain comprehensive medical records with notes, documents, and treatment history.'
                ]
            ],
            'medicines' => [
                'title' => 'Medicine Management Help',
                'sections' => [
                    'Inventory Overview' => 'Monitor medicine stock levels with color-coded status indicators.',
                    'Adding Medicines' => 'Add new medicines with complete information including images, dosage, and medical details.',
                    'Stock Management' => 'Update stock levels, set minimum thresholds, and track stock movements.',
                    'Categories' => 'Organize medicines by therapeutic categories for better management.'
                ]
            ],
            'appointments' => [
                'title' => 'Appointment Management Help',
                'sections' => [
                    'Calendar View' => 'Use the calendar interface to view, create, and manage appointments.',
                    'Scheduling' => 'Book new appointments by clicking on calendar dates or using the new appointment button.',
                    'Status Management' => 'Track appointment status from pending to completion.',
                    'Reminders' => 'System automatically sends appointment reminders to patients.'
                ]
            ],
            'prescriptions' => [
                'title' => 'Prescription Management Help',
                'sections' => [
                    'Creating Prescriptions' => 'Create new prescriptions by selecting patients and adding medicines with dosages.',
                    'Dispensing' => 'Process prescriptions by dispensing medicines and updating stock levels.',
                    'Tracking' => 'Monitor prescription status and dispensing history.',
                    'Reports' => 'Generate prescription reports for analysis and compliance.'
                ]
            ],
            'messages' => [
                'title' => 'Messaging System Help',
                'sections' => [
                    'Patient Communication' => 'Communicate with patients through secure messaging system.',
                    'File Attachments' => 'Share documents, images, and medical reports with patients.',
                    'Message Status' => 'Track message read status and response times.',
                    'Archive Management' => 'Organize conversations by archiving completed discussions.'
                ]
            ]
        ];

        return $helpContent[$module] ?? null;
    }

    /**
     * Search documentation content
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');
        
        if (strlen($query) < 3) {
            return response()->json([
                'results' => [],
                'message' => 'Please enter at least 3 characters to search'
            ]);
        }
        
        $results = [];
        
        // Search in admin guide
        $adminGuidePath = base_path('ADMIN_USAGE_GUIDE.md');
        if (File::exists($adminGuidePath)) {
            $content = File::get($adminGuidePath);
            if (stripos($content, $query) !== false) {
                $results[] = [
                    'title' => 'Admin Usage Guide',
                    'url' => route('documentation.admin-guide'),
                    'description' => 'Complete guide for system administration'
                ];
            }
        }
        
        // Search in quick guide
        $quickGuidePath = base_path('ADMIN_QUICK_GUIDE.md');
        if (File::exists($quickGuidePath)) {
            $content = File::get($quickGuidePath);
            if (stripos($content, $query) !== false) {
                $results[] = [
                    'title' => 'Quick Reference Guide',
                    'url' => route('documentation.quick-guide'),
                    'description' => 'Visual workflows and quick reference'
                ];
            }
        }
        
        return response()->json([
            'results' => $results,
            'query' => $query
        ]);
    }

    /**
     * Export documentation as PDF
     */
    public function exportPdf($type = 'admin-guide')
    {
        try {
            $markdownPath = $type === 'quick-guide' 
                ? base_path('ADMIN_QUICK_GUIDE.md')
                : base_path('ADMIN_USAGE_GUIDE.md');
            
            if (!File::exists($markdownPath)) {
                abort(404, 'Documentation file not found');
            }
            
            $markdownContent = File::get($markdownPath);
            $htmlContent = $this->converter->convert($markdownContent);
            
            $filename = $type === 'quick-guide' 
                ? 'bokod-cms-quick-guide.pdf'
                : 'bokod-cms-admin-guide.pdf';
            
            // Use a simple HTML to PDF approach
            $html = view('documentation.pdf-template', [
                'title' => $type === 'quick-guide' ? 'Quick Reference Guide' : 'Admin Usage Guide',
                'content' => $htmlContent
            ])->render();
            
            return response($html)
                ->header('Content-Type', 'text/html')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to export documentation: ' . $e->getMessage()]);
        }
    }
}