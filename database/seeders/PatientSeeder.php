<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample patients
        $patients = [
            [
                'patient_id' => 'PID-2025-0001',
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'middle_name' => 'Santos',
                'birth_date' => '1985-03-15',
                'gender' => 'Male',
                'contact_number' => '+63 912 345 6789',
                'email' => 'juan.delacruz@email.com',
                'address' => 'Barangay Central, Bokod, Benguet',
                'emergency_contact' => 'Maria Dela Cruz',
                'emergency_phone' => '+63 912 345 6790',
                'medical_history' => 'Hypertension, managed with medication',
                'allergies' => 'Penicillin',
                'blood_type' => 'O+',
                'status' => 'Active'
            ],
            [
                'patient_id' => 'PID-2025-0002',
                'first_name' => 'Maria',
                'last_name' => 'Garcia',
                'middle_name' => 'Luna',
                'birth_date' => '1990-07-22',
                'gender' => 'Female',
                'contact_number' => '+63 912 345 6791',
                'email' => 'maria.garcia@email.com',
                'address' => 'Barangay Poblacion, Bokod, Benguet',
                'emergency_contact' => 'Jose Garcia',
                'emergency_phone' => '+63 912 345 6792',
                'medical_history' => 'No significant medical history',
                'allergies' => 'None known',
                'blood_type' => 'A+',
                'status' => 'Active'
            ],
            [
                'patient_id' => 'PID-2025-0003',
                'first_name' => 'Pedro',
                'last_name' => 'Reyes',
                'middle_name' => 'Torres',
                'birth_date' => '1975-11-08',
                'gender' => 'Male',
                'contact_number' => '+63 912 345 6793',
                'email' => 'pedro.reyes@email.com',
                'address' => 'Barangay Daclan, Bokod, Benguet',
                'emergency_contact' => 'Ana Reyes',
                'emergency_phone' => '+63 912 345 6794',
                'medical_history' => 'Diabetes Type 2, Arthritis',
                'allergies' => 'Sulfa drugs',
                'blood_type' => 'B+',
                'status' => 'Active'
            ],
            [
                'patient_id' => 'PID-2025-0004',
                'first_name' => 'Anna',
                'last_name' => 'Santos',
                'middle_name' => 'Mendoza',
                'birth_date' => '1995-05-12',
                'gender' => 'Female',
                'contact_number' => '+63 912 345 6795',
                'email' => 'anna.santos@email.com',
                'address' => 'Barangay Ekip, Bokod, Benguet',
                'emergency_contact' => 'Roberto Santos',
                'emergency_phone' => '+63 912 345 6796',
                'medical_history' => 'Asthma, well controlled',
                'allergies' => 'Dust, pollen',
                'blood_type' => 'AB+',
                'status' => 'Active'
            ],
            [
                'patient_id' => 'PID-2025-0005',
                'first_name' => 'Carlos',
                'last_name' => 'Villanueva',
                'middle_name' => 'Cruz',
                'birth_date' => '2000-01-30',
                'gender' => 'Male',
                'contact_number' => '+63 912 345 6797',
                'email' => 'carlos.villanueva@email.com',
                'address' => 'Barangay Bobok, Bokod, Benguet',
                'emergency_contact' => 'Elena Villanueva',
                'emergency_phone' => '+63 912 345 6798',
                'medical_history' => 'No significant medical history',
                'allergies' => 'None known',
                'blood_type' => 'O-',
                'status' => 'Active'
            ]
        ];

        foreach ($patients as $patientData) {
            \App\Models\Patient::create($patientData);
        }
    }
}
