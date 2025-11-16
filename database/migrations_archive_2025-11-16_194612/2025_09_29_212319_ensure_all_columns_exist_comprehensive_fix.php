<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Comprehensive fix to ensure all required columns exist across all tables
     */
    public function up(): void
    {
        // 1. USERS TABLE - Ensure all user fields exist
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                // Basic profile fields
                if (!Schema::hasColumn('users', 'phone')) {
                    $table->string('phone', 20)->nullable()->after('email');
                }
                if (!Schema::hasColumn('users', 'date_of_birth')) {
                    $table->date('date_of_birth')->nullable()->after('phone');
                }
                if (!Schema::hasColumn('users', 'gender')) {
                    $table->enum('gender', ['Male', 'Female', 'Other'])->nullable()->after('date_of_birth');
                }
                if (!Schema::hasColumn('users', 'address')) {
                    $table->text('address')->nullable()->after('gender');
                }
                
                // Emergency contact fields
                if (!Schema::hasColumn('users', 'emergency_contact')) {
                    $table->string('emergency_contact')->nullable()->after('address');
                }
                if (!Schema::hasColumn('users', 'emergency_phone')) {
                    $table->string('emergency_phone', 20)->nullable()->after('emergency_contact');
                }
                
                // Medical fields
                if (!Schema::hasColumn('users', 'medical_history')) {
                    $table->text('medical_history')->nullable()->after('emergency_phone');
                }
                if (!Schema::hasColumn('users', 'allergies')) {
                    $table->text('allergies')->nullable()->after('medical_history');
                }
                if (!Schema::hasColumn('users', 'notes')) {
                    $table->text('notes')->nullable()->after('allergies');
                }
                
                // Role and status fields
                if (!Schema::hasColumn('users', 'role')) {
                    $table->enum('role', ['admin', 'patient'])->default('patient')->after('password');
                }
                if (!Schema::hasColumn('users', 'status')) {
                    $table->enum('status', ['active', 'inactive', 'pending', 'rejected', 'suspended'])->default('pending')->after('role');
                }
                if (!Schema::hasColumn('users', 'registration_status')) {
                    $table->enum('registration_status', ['pending', 'approved', 'rejected'])->default('pending')->after('status');
                }
                
                // Profile picture field
                if (!Schema::hasColumn('users', 'profile_picture')) {
                    $table->string('profile_picture')->nullable()->after('registration_status');
                }
                
                // Audit fields
                if (!Schema::hasColumn('users', 'created_by')) {
                    $table->unsignedBigInteger('created_by')->nullable()->after('updated_at');
                }
                if (!Schema::hasColumn('users', 'updated_by')) {
                    $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
                }
            });
        }
        
        // 2. PATIENTS TABLE - Ensure all patient fields exist
        if (Schema::hasTable('patients')) {
            Schema::table('patients', function (Blueprint $table) {
                // Basic fields
                if (!Schema::hasColumn('patients', 'patient_name')) {
                    $table->string('patient_name')->after('id');
                }
                if (!Schema::hasColumn('patients', 'address')) {
                    $table->text('address')->nullable()->after('patient_name');
                }
                if (!Schema::hasColumn('patients', 'position')) {
                    $table->string('position')->nullable()->after('address');
                }
                if (!Schema::hasColumn('patients', 'civil_status')) {
                    $table->enum('civil_status', ['Single', 'Married', 'Divorced', 'Widowed'])->default('Single')->after('position');
                }
                if (!Schema::hasColumn('patients', 'course')) {
                    $table->string('course')->nullable()->after('civil_status');
                }
                
                // Medical measurements
                if (!Schema::hasColumn('patients', 'bmi')) {
                    $table->decimal('bmi', 5, 2)->nullable()->after('course');
                }
                if (!Schema::hasColumn('patients', 'blood_pressure')) {
                    $table->string('blood_pressure')->nullable()->after('bmi');
                }
                
                // Contact info
                if (!Schema::hasColumn('patients', 'contact_person')) {
                    $table->string('contact_person')->nullable()->after('blood_pressure');
                }
                if (!Schema::hasColumn('patients', 'date_of_birth')) {
                    $table->date('date_of_birth')->nullable()->after('contact_person');
                }
                if (!Schema::hasColumn('patients', 'phone_number')) {
                    $table->string('phone_number')->nullable()->after('date_of_birth');
                }
                if (!Schema::hasColumn('patients', 'email')) {
                    $table->string('email')->nullable()->after('phone_number');
                }
                if (!Schema::hasColumn('patients', 'gender')) {
                    $table->enum('gender', ['Male', 'Female', 'Other'])->default('Male')->after('email');
                }
                
                // Medical history fields
                if (!Schema::hasColumn('patients', 'medical_conditions')) {
                    $table->text('medical_conditions')->nullable()->after('gender');
                }
                if (!Schema::hasColumn('patients', 'allergies')) {
                    $table->text('allergies')->nullable()->after('medical_conditions');
                }
                if (!Schema::hasColumn('patients', 'medications')) {
                    $table->text('medications')->nullable()->after('allergies');
                }
                if (!Schema::hasColumn('patients', 'family_history')) {
                    $table->text('family_history')->nullable()->after('medications');
                }
                
                // Emergency contact fields
                if (!Schema::hasColumn('patients', 'emergency_contact_name')) {
                    $table->string('emergency_contact_name')->nullable()->after('family_history');
                }
                if (!Schema::hasColumn('patients', 'emergency_contact_phone')) {
                    $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
                }
                if (!Schema::hasColumn('patients', 'emergency_contact_relationship')) {
                    $table->string('emergency_contact_relationship')->nullable()->after('emergency_contact_phone');
                }
                
                // Profile picture
                if (!Schema::hasColumn('patients', 'profile_picture')) {
                    $table->string('profile_picture')->nullable()->after('emergency_contact_relationship');
                }
                
                // System fields
                if (!Schema::hasColumn('patients', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade')->after('profile_picture');
                }
                if (!Schema::hasColumn('patients', 'archived')) {
                    $table->boolean('archived')->default(false)->after('user_id');
                }
                if (!Schema::hasColumn('patients', 'created_by')) {
                    $table->unsignedBigInteger('created_by')->nullable()->after('updated_at');
                }
                if (!Schema::hasColumn('patients', 'updated_by')) {
                    $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
                }
            });
        }
        
        // 3. APPOINTMENTS TABLE - Ensure all appointment fields exist
        if (Schema::hasTable('appointments')) {
            Schema::table('appointments', function (Blueprint $table) {
                if (!Schema::hasColumn('appointments', 'cancellation_reason')) {
                    $table->text('cancellation_reason')->nullable()->after('reschedule_reason');
                }
                if (!Schema::hasColumn('appointments', 'notes')) {
                    $table->text('notes')->nullable()->after('cancellation_reason');
                }
                if (!Schema::hasColumn('appointments', 'created_by')) {
                    $table->unsignedBigInteger('created_by')->nullable()->after('updated_at');
                }
                if (!Schema::hasColumn('appointments', 'updated_by')) {
                    $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
                }
            });
        }
        
        // 4. MEDICINES TABLE - Ensure all medicine fields exist
        if (Schema::hasTable('medicines')) {
            Schema::table('medicines', function (Blueprint $table) {
                if (!Schema::hasColumn('medicines', 'medicine_image')) {
                    $table->string('medicine_image')->nullable()->after('updated_at');
                }
                if (!Schema::hasColumn('medicines', 'generic_name')) {
                    $table->string('generic_name')->nullable()->after('medicine_name');
                }
                if (!Schema::hasColumn('medicines', 'strength')) {
                    $table->string('strength')->nullable()->after('generic_name');
                }
                if (!Schema::hasColumn('medicines', 'form')) {
                    $table->string('form')->nullable()->after('strength');
                }
                if (!Schema::hasColumn('medicines', 'manufacturer')) {
                    $table->string('manufacturer')->nullable()->after('form');
                }
                if (!Schema::hasColumn('medicines', 'batch_number')) {
                    $table->string('batch_number')->nullable()->after('manufacturer');
                }
                if (!Schema::hasColumn('medicines', 'expiry_date')) {
                    $table->date('expiry_date')->nullable()->after('batch_number');
                }
                if (!Schema::hasColumn('medicines', 'low_stock_threshold')) {
                    $table->integer('low_stock_threshold')->default(10)->after('stock_quantity');
                }
                if (!Schema::hasColumn('medicines', 'physical_count')) {
                    $table->integer('physical_count')->nullable()->after('low_stock_threshold');
                }
                if (!Schema::hasColumn('medicines', 'last_inventory_date')) {
                    $table->timestamp('last_inventory_date')->nullable()->after('physical_count');
                }
            });
        }
        
        // 5. PRESCRIPTIONS TABLE - Ensure all prescription fields exist
        if (Schema::hasTable('prescriptions')) {
            Schema::table('prescriptions', function (Blueprint $table) {
                if (!Schema::hasColumn('prescriptions', 'appointment_id')) {
                    $table->foreignId('appointment_id')->nullable()->constrained('appointments', 'appointment_id')->onDelete('set null')->after('patient_id');
                }
                if (!Schema::hasColumn('prescriptions', 'generic_name')) {
                    $table->string('generic_name')->nullable()->after('medicine_name');
                }
                if (!Schema::hasColumn('prescriptions', 'consultation_type')) {
                    $table->enum('consultation_type', ['follow_up', 'new_patient', 'routine_checkup', 'emergency'])->default('routine_checkup')->after('generic_name');
                }
                if (!Schema::hasColumn('prescriptions', 'unit_price')) {
                    $table->decimal('unit_price', 8, 2)->nullable()->after('status');
                }
                if (!Schema::hasColumn('prescriptions', 'total_amount')) {
                    $table->decimal('total_amount', 8, 2)->nullable()->after('unit_price');
                }
                if (!Schema::hasColumn('prescriptions', 'payment_status')) {
                    $table->enum('payment_status', ['unpaid', 'paid', 'partially_paid'])->default('unpaid')->after('total_amount');
                }
                if (!Schema::hasColumn('prescriptions', 'payment_method')) {
                    $table->string('payment_method')->nullable()->after('payment_status');
                }
                if (!Schema::hasColumn('prescriptions', 'paid_amount')) {
                    $table->decimal('paid_amount', 8, 2)->default(0)->after('payment_method');
                }
            });
        }
        
        // 6. MESSAGES TABLE - Ensure messaging system columns exist
        if (Schema::hasTable('messages')) {
            Schema::table('messages', function (Blueprint $table) {
                if (!Schema::hasColumn('messages', 'attachment_path')) {
                    $table->string('attachment_path')->nullable()->after('message');
                }
                if (!Schema::hasColumn('messages', 'attachment_name')) {
                    $table->string('attachment_name')->nullable()->after('attachment_path');
                }
                if (!Schema::hasColumn('messages', 'attachment_type')) {
                    $table->string('attachment_type')->nullable()->after('attachment_name');
                }
                if (!Schema::hasColumn('messages', 'attachment_size')) {
                    $table->bigInteger('attachment_size')->nullable()->after('attachment_type');
                }
                if (!Schema::hasColumn('messages', 'reactions')) {
                    $table->json('reactions')->nullable()->after('read_at');
                }
            });
        }
        
        // 7. CONVERSATIONS TABLE - Ensure conversation fields exist  
        if (Schema::hasTable('conversations')) {
            Schema::table('conversations', function (Blueprint $table) {
                if (!Schema::hasColumn('conversations', 'archived_by_admin')) {
                    $table->boolean('archived_by_admin')->default(false)->after('updated_at');
                }
                if (!Schema::hasColumn('conversations', 'archived_by_patient')) {
                    $table->boolean('archived_by_patient')->default(false)->after('archived_by_admin');
                }
            });
        }
        
        echo "✅ Comprehensive column check completed!\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't reverse this migration as it only adds missing columns
        // that should exist for the application to function properly
        echo "⚠️  This migration adds essential columns and should not be reversed.\n";
    }
};
