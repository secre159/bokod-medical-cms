<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\Patient;
use Carbon\Carbon;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first patient (assuming patient with user_id 2 exists)
        $patient = Patient::where('user_id', 2)->first();
        
        if ($patient) {
            // Create upcoming appointments
            Appointment::create([
                'patient_id' => $patient->id,
                'appointment_date' => Carbon::tomorrow(),
                'appointment_time' => '10:00:00',
                'reason' => 'Regular checkup and consultation',
                'status' => 'active',
                'approval_status' => 'approved',
                'reschedule_status' => 'none'
            ]);
            
            Appointment::create([
                'patient_id' => $patient->id,
                'appointment_date' => Carbon::today()->addDays(3),
                'appointment_time' => '14:30:00',
                'reason' => 'Follow-up visit for blood pressure monitoring',
                'status' => 'active',
                'approval_status' => 'pending',
                'reschedule_status' => 'none'
            ]);
            
            // Create a completed appointment
            Appointment::create([
                'patient_id' => $patient->id,
                'appointment_date' => Carbon::yesterday(),
                'appointment_time' => '09:00:00',
                'reason' => 'General physical examination',
                'status' => 'completed',
                'approval_status' => 'approved',
                'reschedule_status' => 'none'
            ]);
            
            echo "Test appointments created successfully for patient: {$patient->patient_name}\n";
        } else {
            echo "Patient not found! Make sure to run UserSeeder first.\n";
        }
    }
}
