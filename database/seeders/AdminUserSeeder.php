<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create default admin user
        User::updateOrCreate(
            ['email' => 'admin@bokodcms.com'],
            [
                'name' => 'Administrator',
                'display_name' => 'System Administrator',
                'email' => 'admin@bokodcms.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        
        // Create a sample patient user
        $patientUser = User::updateOrCreate(
            ['email' => 'patient@bokodcms.com'],
            [
                'name' => 'John Doe',
                'display_name' => 'John Doe',
                'email' => 'patient@bokodcms.com',
                'password' => Hash::make('patient123'),
                'role' => 'patient',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        
        // Create corresponding patient record
        Patient::updateOrCreate(
            ['user_id' => $patientUser->id],
            [
                'patient_name' => 'John Doe',
                'email' => 'patient@bokodcms.com',
                'phone_number' => '+1 234-567-8900',
                'date_of_birth' => '1990-01-15',
                'gender' => 'male',
                'address' => '123 Main Street, Bokod City',
                'civil_status' => 'single',
                'archived' => false,
            ]
        );
        
        $this->command->info('Default users created successfully!');
        $this->command->line('Admin credentials:');
        $this->command->line('Email: admin@bokodcms.com');
        $this->command->line('Password: admin123');
        $this->command->line('');
        $this->command->line('Patient credentials:');
        $this->command->line('Email: patient@bokodcms.com');
        $this->command->line('Password: patient123');
    }
}
