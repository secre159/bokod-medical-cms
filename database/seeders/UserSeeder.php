<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user
        \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@bokod.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'Admin',
            'phone' => '+63 912 345 6789',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Create sample doctor
        \App\Models\User::create([
            'name' => 'Dr. Juan Santos',
            'email' => 'doctor@bokod.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'Doctor',
            'phone' => '+63 912 345 6780',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Create sample nurse
        \App\Models\User::create([
            'name' => 'Nurse Maria Cruz',
            'email' => 'nurse@bokod.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'Nurse',
            'phone' => '+63 912 345 6781',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        
        // Create sample pharmacist
        \App\Models\User::create([
            'name' => 'Jose Reyes',
            'email' => 'pharmacist@bokod.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'Pharmacist',
            'phone' => '+63 912 345 6782',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        
        // Create sample receptionist
        \App\Models\User::create([
            'name' => 'Ana Dela Cruz',
            'email' => 'receptionist@bokod.edu.ph',
            'password' => bcrypt('password'),
            'role' => 'Receptionist',
            'phone' => '+63 912 345 6783',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
    }
}
