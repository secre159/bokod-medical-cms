<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Patient;
use App\Models\PatientVisit;
use App\Models\Prescription;
use App\Models\MedicalNote;
use Carbon\Carbon;

class PatientHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $patient = Patient::first();
        
        if (!$patient) {
            $this->command->info('No patients found. Please create patients first.');
            return;
        }

        $this->command->info('Creating sample data for patient: ' . $patient->patient_name);

        // Create patient visits
        PatientVisit::create([
            'patient_id' => $patient->id,
            'visit_date' => now()->subDays(10),
            'next_visit_date' => now()->addDays(20),
            'bp' => '130/85',
            'temperature' => 37.1,
            'pulse_rate' => 82,
            'rr' => 18,
            'spo2' => 96,
            'disease' => 'Hypertension',
            'symptoms' => 'Frequent headaches, fatigue',
            'notes' => 'Initial diagnosis. Patient reports elevated BP readings at home.'
        ]);

        PatientVisit::create([
            'patient_id' => $patient->id,
            'visit_date' => now()->subDays(5),
            'next_visit_date' => now()->addDays(30),
            'bp' => '120/80',
            'temperature' => 36.8,
            'pulse_rate' => 75,
            'rr' => 16,
            'spo2' => 98,
            'disease' => 'Hypertension',
            'symptoms' => 'Mild headache, occasional dizziness',
            'notes' => 'Patient responding well to treatment. Blood pressure stable.'
        ]);

        // Create prescriptions
        Prescription::create([
            'patient_id' => $patient->id,
            'medicine_name' => 'Amlodipine',
            'quantity' => 30,
            'dosage' => 5,
            'frequency' => 'once_daily',
            'duration_days' => 30,
            'instructions' => 'Take one tablet daily in the morning with food',
            'status' => 'active',
            'prescribed_date' => now()->subDays(10),
            'expiry_date' => now()->addDays(20),
            'prescribed_by' => 1,
            'notes' => 'Monitor blood pressure regularly'
        ]);

        Prescription::create([
            'patient_id' => $patient->id,
            'medicine_name' => 'Metformin',
            'quantity' => 60,
            'dosage' => 500,
            'frequency' => 'twice_daily',
            'duration_days' => 60,
            'instructions' => 'Take with meals to reduce stomach upset',
            'status' => 'active',
            'prescribed_date' => now()->subDays(5),
            'expiry_date' => now()->addDays(55),
            'prescribed_by' => 1,
            'notes' => 'For diabetes management'
        ]);

        // Create medical notes
        MedicalNote::create([
            'patient_id' => $patient->id,
            'title' => 'Initial Assessment',
            'content' => 'Patient presents with elevated blood pressure readings. Family history of hypertension. Recommend lifestyle modifications and medication.',
            'note_type' => 'diagnosis',
            'priority' => 'high',
            'created_by' => 1,
            'note_date' => now()->subDays(10),
            'is_private' => false,
            'tags' => json_encode(['hypertension', 'initial-visit', 'diagnosis'])
        ]);

        MedicalNote::create([
            'patient_id' => $patient->id,
            'title' => 'Follow-up Assessment',
            'content' => 'Patient shows significant improvement in blood pressure control. Current medication regimen is effective. Recommend continuing current treatment plan.',
            'note_type' => 'treatment',
            'priority' => 'normal',
            'created_by' => 1,
            'note_date' => now()->subDays(5),
            'is_private' => false,
            'tags' => json_encode(['hypertension', 'follow-up', 'treatment'])
        ]);

        MedicalNote::create([
            'patient_id' => $patient->id,
            'title' => 'Diabetes Screening',
            'content' => 'Blood glucose levels slightly elevated. HbA1c at 6.8%. Starting metformin and recommending dietary counseling.',
            'note_type' => 'observation',
            'priority' => 'normal',
            'created_by' => 1,
            'note_date' => now()->subDays(3),
            'is_private' => false,
            'tags' => json_encode(['diabetes', 'screening', 'medication'])
        ]);

        // Add data for second patient if available
        $patient2 = Patient::skip(1)->first();
        if ($patient2) {
            $this->command->info('Creating additional data for: ' . $patient2->patient_name);
            
            PatientVisit::create([
                'patient_id' => $patient2->id,
                'visit_date' => now()->subDays(7),
                'bp' => '110/70',
                'temperature' => 36.5,
                'pulse_rate' => 68,
                'rr' => 14,
                'spo2' => 99,
                'disease' => 'Annual Checkup',
                'symptoms' => 'No specific complaints',
                'notes' => 'Routine annual physical examination. All vital signs normal.'
            ]);

            MedicalNote::create([
                'patient_id' => $patient2->id,
                'title' => 'Annual Physical',
                'content' => 'Complete physical examination performed. Patient is in good health. All systems normal. Recommend routine follow-up in 12 months.',
                'note_type' => 'general',
                'priority' => 'low',
                'created_by' => 1,
                'note_date' => now()->subDays(7),
                'is_private' => false,
                'tags' => json_encode(['annual-checkup', 'healthy', 'routine'])
            ]);
        }

        $this->command->info('Sample patient history data created successfully!');
    }
}
