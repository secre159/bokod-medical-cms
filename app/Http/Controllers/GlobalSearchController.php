<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Medicine;
use App\Models\Prescription;
use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GlobalSearchController extends Controller
{
    /**
     * Handle global search requests
     */
    public function search(Request $request)
    {
        $query = $request->input('search');
        
        if (empty($query) || strlen(trim($query)) < 2) {
            return redirect()->back()->with('warning', 'Please enter at least 2 characters to search.');
        }
        
        $query = trim($query);
        $user = Auth::user();
        $results = [];
        
        if ($user->role === 'admin') {
            $results = $this->adminSearch($query);
        } else {
            $results = $this->patientSearch($query, $user);
        }
        
        return view('search.results', compact('results', 'query'));
    }
    
    /**
     * Admin search across all entities
     */
    private function adminSearch($query)
    {
        $results = [
            'patients' => [],
            'appointments' => [],
            'medicines' => [],
            'prescriptions' => [],
            'users' => [],
            'messages' => [],
            'total' => 0
        ];
        
        // Search patients
        $patients = Patient::where('patient_name', 'LIKE', "%{$query}%")
                          ->orWhere('email', 'LIKE', "%{$query}%")
                          ->orWhere('phone_number', 'LIKE', "%{$query}%")
                          ->orWhere('address', 'LIKE', "%{$query}%")
                          ->with('user')
                          ->limit(10)
                          ->get();
        $results['patients'] = $patients;
        $results['total'] += $patients->count();
        
        // Search appointments
        $appointments = Appointment::whereHas('patient', function($q) use ($query) {
                              $q->where('patient_name', 'LIKE', "%{$query}%");
                          })
                          ->orWhere('reason', 'LIKE', "%{$query}%")
                          ->orWhere('diagnosis', 'LIKE', "%{$query}%")
                          ->orWhere('notes', 'LIKE', "%{$query}%")
                          ->with(['patient'])
                          ->limit(10)
                          ->get();
        $results['appointments'] = $appointments;
        $results['total'] += $appointments->count();
        
        // Search medicines
        $medicines = Medicine::where('name', 'LIKE', "%{$query}%")
                           ->orWhere('generic_name', 'LIKE', "%{$query}%")
                           ->orWhere('brand_name', 'LIKE', "%{$query}%")
                           ->orWhere('description', 'LIKE', "%{$query}%")
                           ->orWhere('indication', 'LIKE', "%{$query}%")
                           ->limit(10)
                           ->get();
        $results['medicines'] = $medicines;
        $results['total'] += $medicines->count();
        
        // Search prescriptions
        $prescriptions = Prescription::where('medication_name', 'LIKE', "%{$query}%")
                                   ->orWhere('dosage', 'LIKE', "%{$query}%")
                                   ->orWhere('instructions', 'LIKE', "%{$query}%")
                                   ->orWhereHas('patient', function($q) use ($query) {
                                       $q->where('patient_name', 'LIKE', "%{$query}%");
                                   })
                                   ->with(['patient', 'medicine'])
                                   ->limit(10)
                                   ->get();
        $results['prescriptions'] = $prescriptions;
        $results['total'] += $prescriptions->count();
        
        // Search users
        $users = User::where('name', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%")
                    ->orWhere('phone', 'LIKE', "%{$query}%")
                    ->limit(10)
                    ->get();
        $results['users'] = $users;
        $results['total'] += $users->count();
        
        // Search messages (recent conversations)
        $conversations = Conversation::whereHas('messages', function($q) use ($query) {
                                      $q->where('message', 'LIKE', "%{$query}%");
                                  })
                                  ->orWhereHas('patient', function($q) use ($query) {
                                      $q->where('name', 'LIKE', "%{$query}%");
                                  })
                                  ->with(['patient', 'admin', 'latestMessage'])
                                  ->limit(10)
                                  ->get();
        $results['messages'] = $conversations;
        $results['total'] += $conversations->count();
        
        return $results;
    }
    
    /**
     * Patient search (limited to their own data)
     */
    private function patientSearch($query, $user)
    {
        $patient = $user->patient;
        
        if (!$patient) {
            return [
                'appointments' => [],
                'prescriptions' => [],
                'messages' => [],
                'total' => 0
            ];
        }
        
        $results = [
            'appointments' => [],
            'prescriptions' => [],
            'messages' => [],
            'total' => 0
        ];
        
        // Search patient's appointments
        $appointments = $patient->appointments()
                              ->where(function($q) use ($query) {
                                  $q->where('reason', 'LIKE', "%{$query}%")
                                    ->orWhere('diagnosis', 'LIKE', "%{$query}%")
                                    ->orWhere('notes', 'LIKE', "%{$query}%");
                              })
                              ->limit(10)
                              ->get();
        $results['appointments'] = $appointments;
        $results['total'] += $appointments->count();
        
        // Search patient's prescriptions
        $prescriptions = Prescription::where('patient_id', $patient->id)
                                   ->where(function($q) use ($query) {
                                       $q->where('medication_name', 'LIKE', "%{$query}%")
                                         ->orWhere('dosage', 'LIKE', "%{$query}%")
                                         ->orWhere('instructions', 'LIKE', "%{$query}%");
                                   })
                                   ->with(['medicine'])
                                   ->limit(10)
                                   ->get();
        $results['prescriptions'] = $prescriptions;
        $results['total'] += $prescriptions->count();
        
        // Search patient's messages
        $conversations = Conversation::where('patient_id', $user->id)
                                   ->whereHas('messages', function($q) use ($query) {
                                       $q->where('message', 'LIKE', "%{$query}%");
                                   })
                                   ->with(['admin', 'latestMessage'])
                                   ->limit(10)
                                   ->get();
        $results['messages'] = $conversations;
        $results['total'] += $conversations->count();
        
        return $results;
    }
}
