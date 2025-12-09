<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add new name fields to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name', 100)->nullable()->after('name');
            $table->string('middle_name', 100)->nullable()->after('first_name');
            $table->string('last_name', 100)->nullable()->after('middle_name');
        });
        
        // Add new name fields to patients table
        Schema::table('patients', function (Blueprint $table) {
            $table->string('first_name', 100)->nullable()->after('patient_name');
            $table->string('middle_name', 100)->nullable()->after('first_name');
            $table->string('last_name', 100)->nullable()->after('middle_name');
        });
        
        // Migrate existing data for users
        $users = DB::table('users')->whereNotNull('name')->get();
        foreach ($users as $user) {
            $nameParts = $this->parseFullName($user->name);
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'first_name' => $nameParts['first_name'],
                    'middle_name' => $nameParts['middle_name'],
                    'last_name' => $nameParts['last_name'],
                ]);
        }
        
        // Migrate existing data for patients
        $patients = DB::table('patients')->whereNotNull('patient_name')->get();
        foreach ($patients as $patient) {
            $nameParts = $this->parseFullName($patient->patient_name);
            DB::table('patients')
                ->where('id', $patient->id)
                ->update([
                    'first_name' => $nameParts['first_name'],
                    'middle_name' => $nameParts['middle_name'],
                    'last_name' => $nameParts['last_name'],
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'middle_name', 'last_name']);
        });
        
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'middle_name', 'last_name']);
        });
    }
    
    /**
     * Parse a full name into first, middle, and last name components
     */
    private function parseFullName(?string $fullName): array
    {
        if (empty($fullName)) {
            return [
                'first_name' => null,
                'middle_name' => null,
                'last_name' => null,
            ];
        }
        
        $fullName = trim($fullName);
        $parts = array_values(array_filter(explode(' ', $fullName)));
        
        if (empty($parts)) {
            return [
                'first_name' => null,
                'middle_name' => null,
                'last_name' => null,
            ];
        }
        
        if (count($parts) === 1) {
            // Only one name - treat as first name
            return [
                'first_name' => $parts[0],
                'middle_name' => null,
                'last_name' => null,
            ];
        }
        
        if (count($parts) === 2) {
            // Two names - first and last
            return [
                'first_name' => $parts[0],
                'middle_name' => null,
                'last_name' => $parts[1],
            ];
        }
        
        // Three or more names
        $firstName = array_shift($parts);
        $lastName = array_pop($parts);
        $middleName = implode(' ', $parts);
        
        return [
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'last_name' => $lastName,
        ];
    }
};
