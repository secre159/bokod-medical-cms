<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use App\Models\User;
use App\Models\Patient;
use Carbon\Carbon;

class PatientRegistrationController extends Controller
{
    /**
     * Show the patient registration form
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle patient self-registration
     */
    public function register(Request $request)
    {
        $request->validate([
            // User account info
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            
            // Student/Patient specific info
            'student_id' => ['required', 'string', 'max:20', 'unique:patients,position'],
            'course' => ['required', 'string', 'max:255'],
            'year_level' => ['required', 'string', 'max:20'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:Male,Female,Other'],
            'phone_number' => ['required', 'string', 'max:15'],
            'address' => ['required', 'string', 'max:500'],
            
            // Emergency contact
            'emergency_contact_name' => ['required', 'string', 'max:255'],
            'emergency_contact_relationship' => ['required', 'string', 'max:100'],
            'emergency_contact_phone' => ['required', 'string', 'max:15'],
            'emergency_contact_address' => ['required', 'string', 'max:500'],
            
            // Optional health info
            'height' => ['nullable', 'numeric', 'min:50', 'max:250'],
            'weight' => ['nullable', 'numeric', 'min:20', 'max:300'],
            'civil_status' => ['nullable', 'in:Single,Married,Divorced,Widowed'],
            
            // Agreement
            'terms_agreement' => ['required', 'accepted'],
            'privacy_agreement' => ['required', 'accepted'],
        ]);

        try {
            DB::beginTransaction();

            // All registrations require manual approval by admin
            // Create user account
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'patient',
                'status' => 'inactive', // Inactive until approved (matches DB constraint)
                'registration_status' => User::REGISTRATION_PENDING,
                'registration_source' => User::SOURCE_SELF,
                // approved_at will default to null automatically
                'email_verified_at' => null,
            ]);

            // Calculate BMI if height and weight provided
            $bmi = null;
            if ($request->height && $request->weight) {
                $heightInMeters = $request->height / 100;
                $bmi = round($request->weight / ($heightInMeters * $heightInMeters), 2);
            }

            // Create patient record
            $patient = Patient::create([
                'patient_name' => $request->name,
                'position' => $request->student_id, // Using position field for student ID
                'course' => $request->course,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'address' => $request->address,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_relationship' => $request->emergency_contact_relationship,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'emergency_contact_address' => $request->emergency_contact_address,
                'height' => $request->height,
                'weight' => $request->weight,
                'bmi' => $bmi,
                'civil_status' => $request->civil_status ?? 'Single',
                'user_id' => $user->id,
                'archived' => false,
            ]);

            DB::commit();

            // All registrations require admin approval
            return redirect()->route('patient.registration.success')
                           ->with('success', 'Registration submitted successfully! Your account is pending admin approval. You will receive an email notification once your registration is reviewed.');

        } catch (\Exception $e) {
            DB::rollback();
            
            // Log the actual error for debugging
            \Log::error('Patient registration failed', [
                'error' => $e->getMessage(),
                'email' => $request->email,
                'name' => $request->name,
                'trace' => $e->getTraceAsString()
            ]);
            
            // Show more specific error message if it's a database constraint issue
            $errorMessage = 'Registration failed. Please try again.';
            
            if (str_contains($e->getMessage(), 'Duplicate entry') || str_contains($e->getMessage(), 'unique constraint')) {
                $errorMessage = 'This email address or student ID is already registered. Please use a different email or contact support if you believe this is an error.';
            } elseif (str_contains($e->getMessage(), 'foreign key constraint')) {
                $errorMessage = 'Database relationship error. Please contact support for assistance.';
            }
            
            return back()
                ->withInput()
                ->with('error', $errorMessage . ' If the problem persists, contact the health center.');
        }
    }

    /**
     * Check if email is a verified BSU email
     */
    private function isVerifiedBSUEmail($email)
    {
        $bsuDomains = [
            '@bsu.edu.ph',
            '@student.bsu.edu.ph',
            '@faculty.bsu.edu.ph',
            '@staff.bsu.edu.ph'
        ];

        foreach ($bsuDomains as $domain) {
            if (str_ends_with(strtolower($email), $domain)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Show registration success page
     */
    public function registrationSuccess()
    {
        return view('auth.registration-success');
    }
}