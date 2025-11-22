<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Patient;
use App\Models\Medicine;
use App\Models\Appointment;
use App\Models\Prescription;
use Carbon\Carbon;
use App\Helpers\TimezoneHelper;

class ComprehensiveDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting comprehensive data seeding...');
        
        DB::beginTransaction();
        
        try {
            // Seed in order of dependencies
            $this->seedAdminUsers();
            $this->seedPatientUsers();
            $this->seedMedicines();
            $this->seedAppointments();
            $this->seedPrescriptions();
            
            DB::commit();
            $this->command->info('✅ Database seeded successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Seeding failed: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Seed admin users
     */
    private function seedAdminUsers(): void
    {
        $this->command->info('👤 Seeding admin users...');
        
        $admins = [
            [
                'name' => 'Admin User',
                'email' => 'admin@bokod.edu.ph',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
                'registration_status' => 'approved',
                'registration_source' => 'admin',
                'approved_at' => now(),
            ],
            [
                'name' => 'Dr. Maria Santos',
                'email' => 'maria.santos@bokod.edu.ph',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
                'registration_status' => 'approved',
                'registration_source' => 'admin',
                'phone' => '09171234567',
                'gender' => 'female',
                'approved_at' => now(),
            ],
            [
                'name' => 'Nurse John Reyes',
                'email' => 'john.reyes@bokod.edu.ph',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
                'registration_status' => 'approved',
                'registration_source' => 'admin',
                'phone' => '09181234567',
                'gender' => 'male',
                'approved_at' => now(),
            ],
        ];
        
        foreach ($admins as $admin) {
            User::create($admin);
        }
        
        $this->command->info('  ✓ Created ' . count($admins) . ' admin users');
    }
    
    /**
     * Seed patient users with patient records
     */
    private function seedPatientUsers(): void
    {
        $this->command->info('🏥 Seeding patient users...');
        
        $patients = [
            [
                'name' => 'Juan Dela Cruz',
                'email' => 'juan.delacruz@student.bokod.edu.ph',
                'phone' => '09171111111',
                'date_of_birth' => '2003-05-15',
                'gender' => 'Male',
                'address' => 'Barangay Poblacion, Bokod, Benguet',
                'course' => 'BS Computer Science',
                'civil_status' => 'Single',
                'height' => 170,
                'weight' => 65,
                'emergency_contact_name' => 'Maria Dela Cruz',
                'emergency_contact_relationship' => 'Mother',
                'emergency_contact_phone' => '09181111111',
            ],
            [
                'name' => 'Maria Clara Santos',
                'email' => 'maria.santos@student.bokod.edu.ph',
                'phone' => '09172222222',
                'date_of_birth' => '2004-08-20',
                'gender' => 'Female',
                'address' => 'Barangay Tikey, Bokod, Benguet',
                'course' => 'BS Education',
                'civil_status' => 'Single',
                'height' => 158,
                'weight' => 52,
                'systolic_bp' => 110,
                'diastolic_bp' => 70,
                'emergency_contact_name' => 'Jose Santos',
                'emergency_contact_relationship' => 'Father',
                'emergency_contact_phone' => '09182222222',
            ],
            [
                'name' => 'Pedro Gonzales',
                'email' => 'pedro.gonzales@student.bokod.edu.ph',
                'phone' => '09173333333',
                'date_of_birth' => '2002-12-10',
                'gender' => 'Male',
                'address' => 'Barangay Daclan, Bokod, Benguet',
                'course' => 'BS Business Administration',
                'civil_status' => 'Single',
                'height' => 175,
                'weight' => 70,
                'medical_history' => 'Asthma since childhood',
                'allergies' => 'Peanuts, Shellfish',
                'emergency_contact_name' => 'Ana Gonzales',
                'emergency_contact_relationship' => 'Mother',
                'emergency_contact_phone' => '09183333333',
            ],
            [
                'name' => 'Ana Rodriguez',
                'email' => 'ana.rodriguez@student.bokod.edu.ph',
                'phone' => '09174444444',
                'date_of_birth' => '2003-03-25',
                'gender' => 'Female',
                'address' => 'Barangay Ekip, Bokod, Benguet',
                'course' => 'BS Nursing',
                'civil_status' => 'Single',
                'height' => 162,
                'weight' => 55,
                'systolic_bp' => 115,
                'diastolic_bp' => 75,
                'emergency_contact_name' => 'Carlos Rodriguez',
                'emergency_contact_relationship' => 'Father',
                'emergency_contact_phone' => '09184444444',
            ],
            [
                'name' => 'Jose Bautista',
                'email' => 'jose.bautista@student.bokod.edu.ph',
                'phone' => '09175555555',
                'date_of_birth' => '2005-07-18',
                'gender' => 'Male',
                'address' => 'Barangay Ambuclao, Bokod, Benguet',
                'course' => 'BS Information Technology',
                'civil_status' => 'Single',
                'height' => 168,
                'weight' => 62,
                'emergency_contact_name' => 'Elena Bautista',
                'emergency_contact_relationship' => 'Mother',
                'emergency_contact_phone' => '09185555555',
            ],
        ];
        
        foreach ($patients as $patientData) {
            // Calculate BMI if height and weight are provided
            if (isset($patientData['height']) && isset($patientData['weight'])) {
                $heightInMeters = $patientData['height'] / 100;
                $patientData['bmi'] = round($patientData['weight'] / ($heightInMeters * $heightInMeters), 2);
            }
            
            // Create user account
            $user = User::create([
                'name' => $patientData['name'],
                'email' => $patientData['email'],
                'password' => Hash::make('password'),
                'role' => 'patient',
                'status' => 'active',
                'registration_status' => 'approved',
                'registration_source' => 'self',
                'phone' => $patientData['phone'] ?? null,
                'date_of_birth' => $patientData['date_of_birth'] ?? null,
                'gender' => $patientData['gender'] ?? null,
                'address' => $patientData['address'] ?? null,
                'emergency_contact' => $patientData['emergency_contact_name'] ?? null,
                'emergency_phone' => $patientData['emergency_contact_phone'] ?? null,
                'medical_history' => $patientData['medical_history'] ?? null,
                'allergies' => $patientData['allergies'] ?? null,
                'approved_at' => now(),
            ]);
            
            // Create patient record
            Patient::create([
                'user_id' => $user->id,
                'patient_name' => $patientData['name'],
                'email' => $patientData['email'],
                'phone_number' => $patientData['phone'] ?? null,
                'phone' => $patientData['phone'] ?? null,
                'date_of_birth' => $patientData['date_of_birth'] ?? null,
                'gender' => $patientData['gender'] ?? null,
                'address' => $patientData['address'] ?? null,
                'course' => $patientData['course'] ?? null,
                'civil_status' => $patientData['civil_status'] ?? 'Single',
                'height' => $patientData['height'] ?? null,
                'weight' => $patientData['weight'] ?? null,
                'bmi' => $patientData['bmi'] ?? null,
                'systolic_bp' => $patientData['systolic_bp'] ?? null,
                'diastolic_bp' => $patientData['diastolic_bp'] ?? null,
                'emergency_contact_name' => $patientData['emergency_contact_name'] ?? null,
                'emergency_contact_relationship' => $patientData['emergency_contact_relationship'] ?? null,
                'emergency_contact_phone' => $patientData['emergency_contact_phone'] ?? null,
                'medical_history' => $patientData['medical_history'] ?? null,
                'allergies' => $patientData['allergies'] ?? null,
                'archived' => false,
            ]);
        }
        
        $this->command->info('  ✓ Created ' . count($patients) . ' patient users with records');
    }
    
    /**
     * Seed medicines
     */
    private function seedMedicines(): void
    {
        $this->command->info('💊 Seeding medicines...');
        
        $medicines = [
            [
                'medicine_name' => 'Paracetamol 500mg',
                'generic_name' => 'Paracetamol',
                'brand_name' => 'Biogesic',
                'category' => 'Pain Relief',
                'dosage_form' => 'Tablet',
                'strength' => '500mg',
                'therapeutic_class' => 'Analgesic',
                'description' => 'Pain reliever and fever reducer',
                'indication' => 'For the relief of minor aches and pains and reduction of fever',
                'dosage_instructions' => 'Take 1-2 tablets every 4-6 hours as needed',
                'side_effects' => 'Rare: allergic reactions, liver damage with overdose',
                'contraindications' => 'Severe liver disease',
                'stock_quantity' => 500,
                'minimum_stock' => 100,
                'unit' => 'tablets',
                'requires_prescription' => false,
                'status' => 'active',
            ],
            [
                'medicine_name' => 'Amoxicillin 500mg',
                'generic_name' => 'Amoxicillin',
                'brand_name' => 'Amoxil',
                'category' => 'Antibiotics',
                'dosage_form' => 'Capsule',
                'strength' => '500mg',
                'therapeutic_class' => 'Antibiotic',
                'description' => 'Penicillin-type antibiotic',
                'indication' => 'Treatment of bacterial infections',
                'dosage_instructions' => 'Take 1 capsule 3 times daily for 7 days',
                'side_effects' => 'Nausea, diarrhea, allergic reactions',
                'contraindications' => 'Penicillin allergy',
                'stock_quantity' => 300,
                'minimum_stock' => 50,
                'unit' => 'capsules',
                'requires_prescription' => true,
                'status' => 'active',
            ],
            [
                'medicine_name' => 'Cetirizine 10mg',
                'generic_name' => 'Cetirizine',
                'brand_name' => 'Zyrtec',
                'category' => 'Cold & Flu',
                'dosage_form' => 'Tablet',
                'strength' => '10mg',
                'therapeutic_class' => 'Antihistamine',
                'description' => 'Antihistamine for allergic reactions',
                'indication' => 'Relief of allergy symptoms',
                'dosage_instructions' => 'Take 1 tablet once daily',
                'side_effects' => 'Drowsiness, dry mouth',
                'stock_quantity' => 200,
                'minimum_stock' => 40,
                'unit' => 'tablets',
                'requires_prescription' => false,
                'status' => 'active',
            ],
            [
                'medicine_name' => 'Multivitamins',
                'generic_name' => 'Multivitamins + Minerals',
                'brand_name' => 'Centrum',
                'category' => 'Vitamins & Supplements',
                'dosage_form' => 'Tablet',
                'strength' => 'Standard',
                'therapeutic_class' => 'Vitamin/Supplement',
                'description' => 'Daily multivitamin supplement',
                'indication' => 'Nutritional supplementation',
                'dosage_instructions' => 'Take 1 tablet daily with meal',
                'stock_quantity' => 400,
                'minimum_stock' => 80,
                'unit' => 'tablets',
                'requires_prescription' => false,
                'status' => 'active',
            ],
            [
                'medicine_name' => 'Ibuprofen 400mg',
                'generic_name' => 'Ibuprofen',
                'brand_name' => 'Advil',
                'category' => 'Pain Relief',
                'dosage_form' => 'Tablet',
                'strength' => '400mg',
                'therapeutic_class' => 'Anti-inflammatory',
                'description' => 'Nonsteroidal anti-inflammatory drug',
                'indication' => 'Pain, inflammation, fever reduction',
                'dosage_instructions' => 'Take 1 tablet every 6-8 hours as needed',
                'side_effects' => 'Stomach upset, heartburn',
                'contraindications' => 'Active peptic ulcer, severe heart failure',
                'stock_quantity' => 350,
                'minimum_stock' => 70,
                'unit' => 'tablets',
                'requires_prescription' => false,
                'status' => 'active',
            ],
            [
                'medicine_name' => 'Omeprazole 20mg',
                'generic_name' => 'Omeprazole',
                'brand_name' => 'Losec',
                'category' => 'Digestive Health',
                'dosage_form' => 'Capsule',
                'strength' => '20mg',
                'therapeutic_class' => 'Antacid',
                'description' => 'Proton pump inhibitor for acid reduction',
                'indication' => 'Treatment of gastric ulcers and GERD',
                'dosage_instructions' => 'Take 1 capsule once daily before breakfast',
                'side_effects' => 'Headache, nausea, diarrhea',
                'stock_quantity' => 250,
                'minimum_stock' => 50,
                'unit' => 'capsules',
                'requires_prescription' => true,
                'status' => 'active',
            ],
            [
                'medicine_name' => 'Salbutamol Inhaler',
                'generic_name' => 'Salbutamol',
                'brand_name' => 'Ventolin',
                'category' => 'Asthma & Respiratory',
                'dosage_form' => 'Inhaler',
                'strength' => '100mcg/puff',
                'therapeutic_class' => 'Bronchodilator',
                'description' => 'Relieves bronchospasm in asthma',
                'indication' => 'Asthma, COPD',
                'dosage_instructions' => '1-2 puffs as needed for breathing difficulty',
                'side_effects' => 'Tremor, palpitations, headache',
                'stock_quantity' => 50,
                'minimum_stock' => 10,
                'unit' => 'inhalers',
                'requires_prescription' => true,
                'status' => 'active',
            ],
            [
                'medicine_name' => 'Loperamide 2mg',
                'generic_name' => 'Loperamide',
                'brand_name' => 'Imodium',
                'category' => 'Digestive Health',
                'dosage_form' => 'Capsule',
                'strength' => '2mg',
                'therapeutic_class' => 'Antidiarrheal',
                'description' => 'Treatment of acute diarrhea',
                'indication' => 'Symptomatic relief of diarrhea',
                'dosage_instructions' => 'Initial: 2 capsules, then 1 after each loose stool',
                'side_effects' => 'Constipation, dizziness, nausea',
                'stock_quantity' => 180,
                'minimum_stock' => 30,
                'unit' => 'capsules',
                'requires_prescription' => false,
                'status' => 'active',
            ],
        ];
        
        foreach ($medicines as $medicine) {
            Medicine::create($medicine);
        }
        
        $this->command->info('  ✓ Created ' . count($medicines) . ' medicines');
    }
    
    /**
     * Seed appointments
     */
    private function seedAppointments(): void
    {
        $this->command->info('📅 Seeding appointments...');
        
        $patients = Patient::all();
        $today = TimezoneHelper::now();
        
        if ($patients->isEmpty()) {
            $this->command->warn('  ⚠ No patients found. Skipping appointments.');
            return;
        }
        
        $appointments = [
            // Today's appointments
            [
                'patient_id' => $patients->random()->id,
                'appointment_date' => $today->copy()->toDateString(),
                'appointment_time' => '09:00',
                'reason' => 'General checkup and consultation',
                'status' => 'active',
                'approval_status' => 'approved',
            ],
            [
                'patient_id' => $patients->random()->id,
                'appointment_date' => $today->copy()->toDateString(),
                'appointment_time' => '10:30',
                'reason' => 'Follow-up consultation for fever',
                'status' => 'active',
                'approval_status' => 'approved',
            ],
            // Tomorrow's appointments
            [
                'patient_id' => $patients->random()->id,
                'appointment_date' => $today->copy()->addDay()->toDateString(),
                'appointment_time' => '14:00',
                'reason' => 'Cough and cold symptoms',
                'status' => 'active',
                'approval_status' => 'approved',
            ],
            [
                'patient_id' => $patients->random()->id,
                'appointment_date' => $today->copy()->addDay()->toDateString(),
                'appointment_time' => '15:30',
                'reason' => 'Headache and dizziness',
                'status' => 'active',
                'approval_status' => 'pending',
            ],
            // Next week's appointments
            [
                'patient_id' => $patients->random()->id,
                'appointment_date' => $today->copy()->addDays(3)->toDateString(),
                'appointment_time' => '09:00',
                'reason' => 'Medical certificate request',
                'status' => 'active',
                'approval_status' => 'approved',
            ],
            [
                'patient_id' => $patients->random()->id,
                'appointment_date' => $today->copy()->addDays(5)->toDateString(),
                'appointment_time' => '13:00',
                'reason' => 'Stomach pain consultation',
                'status' => 'active',
                'approval_status' => 'pending',
            ],
            // Completed appointments (past)
            [
                'patient_id' => $patients->random()->id,
                'appointment_date' => $today->copy()->subDays(7)->toDateString(),
                'appointment_time' => '10:00',
                'reason' => 'Flu symptoms',
                'status' => 'completed',
                'approval_status' => 'approved',
                'diagnosis' => 'Upper respiratory tract infection',
                'treatment_notes' => 'Prescribed antibiotics and rest',
            ],
            [
                'patient_id' => $patients->random()->id,
                'appointment_date' => $today->copy()->subDays(14)->toDateString(),
                'appointment_time' => '14:00',
                'reason' => 'Annual physical exam',
                'status' => 'completed',
                'approval_status' => 'approved',
                'diagnosis' => 'Healthy, no significant findings',
            ],
        ];
        
        foreach ($appointments as $appointment) {
            Appointment::create($appointment);
        }
        
        $this->command->info('  ✓ Created ' . count($appointments) . ' appointments');
    }
    
    /**
     * Seed prescriptions
     */
    private function seedPrescriptions(): void
    {
        $this->command->info('💉 Seeding prescriptions...');
        
        $patients = Patient::all();
        $medicines = Medicine::all();
        $admin = User::where('role', 'admin')->first();
        
        if ($patients->isEmpty() || $medicines->isEmpty() || !$admin) {
            $this->command->warn('  ⚠ Missing required data. Skipping prescriptions.');
            return;
        }
        
        $today = TimezoneHelper::now();
        
        $prescriptions = [
            [
                'patient_id' => $patients->random()->id,
                'medicine_id' => $medicines->where('medicine_name', 'Paracetamol 500mg')->first()->id,
                'medicine_name' => 'Paracetamol 500mg',
                'generic_name' => 'Paracetamol',
                'quantity' => 20,
                'dosage' => '500mg',
                'frequency' => 'three_times_daily',
                'instructions' => 'Take 1 tablet three times a day after meals for fever',
                'prescribed_date' => $today->copy()->subDays(3)->toDateString(),
                'expiry_date' => $today->copy()->addDays(27)->toDateString(),
                'status' => 'active',
                'prescribed_by' => $admin->id,
            ],
            [
                'patient_id' => $patients->random()->id,
                'medicine_id' => $medicines->where('medicine_name', 'Amoxicillin 500mg')->first()->id,
                'medicine_name' => 'Amoxicillin 500mg',
                'generic_name' => 'Amoxicillin',
                'quantity' => 21,
                'dosage' => '500mg',
                'frequency' => 'three_times_daily',
                'instructions' => 'Take 1 capsule three times daily for 7 days. Complete the course.',
                'prescribed_date' => $today->copy()->subDays(2)->toDateString(),
                'expiry_date' => $today->copy()->addDays(28)->toDateString(),
                'status' => 'active',
                'prescribed_by' => $admin->id,
                'notes' => 'For bacterial infection',
            ],
            [
                'patient_id' => $patients->random()->id,
                'medicine_id' => $medicines->where('medicine_name', 'Cetirizine 10mg')->first()->id,
                'medicine_name' => 'Cetirizine 10mg',
                'generic_name' => 'Cetirizine',
                'quantity' => 14,
                'dosage' => '10mg',
                'frequency' => 'once_daily',
                'instructions' => 'Take 1 tablet once daily, preferably in the evening',
                'prescribed_date' => $today->copy()->subDays(1)->toDateString(),
                'expiry_date' => $today->copy()->addDays(29)->toDateString(),
                'status' => 'active',
                'prescribed_by' => $admin->id,
            ],
            [
                'patient_id' => $patients->random()->id,
                'medicine_id' => $medicines->where('medicine_name', 'Salbutamol Inhaler')->first()->id,
                'medicine_name' => 'Salbutamol Inhaler',
                'generic_name' => 'Salbutamol',
                'quantity' => 1,
                'dosage' => '100mcg/puff',
                'frequency' => 'as_needed',
                'instructions' => '1-2 puffs when experiencing difficulty breathing. Use as needed.',
                'prescribed_date' => $today->copy()->subDays(10)->toDateString(),
                'expiry_date' => $today->copy()->addDays(50)->toDateString(),
                'status' => 'active',
                'prescribed_by' => $admin->id,
                'notes' => 'For asthma management',
            ],
            // Consultation without medicine
            [
                'patient_id' => $patients->random()->id,
                'medicine_id' => null,
                'medicine_name' => 'Consultation: General Consultation',
                'generic_name' => null,
                'quantity' => 1,
                'dosage' => 'Not Applicable',
                'frequency' => 'not_applicable',
                'instructions' => 'Rest and hydration. Monitor symptoms. Return if condition worsens.',
                'prescribed_date' => $today->copy()->subDays(1)->toDateString(),
                'expiry_date' => $today->copy()->addDays(29)->toDateString(),
                'status' => 'active',
                'prescribed_by' => $admin->id,
                'consultation_type' => 'general_consultation',
                'notes' => 'Viral infection, no medication needed',
            ],
            // Completed prescription
            [
                'patient_id' => $patients->random()->id,
                'medicine_id' => $medicines->where('medicine_name', 'Ibuprofen 400mg')->first()->id,
                'medicine_name' => 'Ibuprofen 400mg',
                'generic_name' => 'Ibuprofen',
                'quantity' => 10,
                'dosage' => '400mg',
                'frequency' => 'twice_daily',
                'instructions' => 'Take 1 tablet twice daily after meals',
                'prescribed_date' => $today->copy()->subDays(20)->toDateString(),
                'expiry_date' => $today->copy()->subDays(5)->toDateString(),
                'status' => 'completed',
                'dispensed_quantity' => 10,
                'prescribed_by' => $admin->id,
            ],
        ];
        
        foreach ($prescriptions as $prescription) {
            Prescription::create($prescription);
        }
        
        $this->command->info('  ✓ Created ' . count($prescriptions) . ' prescriptions');
    }
}
