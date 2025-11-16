<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This comprehensive migration creates all tables with the exact schema
     * from the production database backup.
     */
    public function up(): void
    {
        // Only run if tables don't already exist (for fresh installations)
        
        // Users Table
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->string('remember_token', 100)->nullable();
                $table->timestamps();
                $table->string('role')->default('user');
                $table->timestamp('last_login_at')->nullable();
                $table->string('status', 20)->default('active');
                $table->string('registration_status', 20)->default('approved');
                $table->string('registration_source', 20)->default('self');
                $table->string('phone', 20)->nullable();
                $table->date('date_of_birth')->nullable();
                $table->string('gender', 10)->nullable();
                $table->text('address')->nullable();
                $table->string('emergency_contact')->nullable();
                $table->string('emergency_phone', 20)->nullable();
                $table->text('medical_history')->nullable();
                $table->text('allergies')->nullable();
                $table->text('notes')->nullable();
                $table->string('avatar')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->string('display_name')->nullable();
                $table->string('profile_picture')->nullable();
                
                // Indexes
                $table->index('registration_status', 'idx_users_registration_status');
                $table->index('role', 'idx_users_role');
                $table->index('status', 'idx_users_status');
                
                // Foreign keys
                $table->foreign('approved_by', 'fk_users_approved_by')
                      ->references('id')->on('users')->onDelete('set null');
            });
        }
        
        // Patients Table
        if (!Schema::hasTable('patients')) {
            Schema::create('patients', function (Blueprint $table) {
                $table->id();
                $table->string('patient_name');
                $table->text('address')->nullable();
                $table->string('position')->nullable();
                $table->string('civil_status', 20)->nullable()->default('Single');
                $table->string('course')->nullable();
                $table->decimal('bmi', 5, 2)->nullable();
                $table->string('blood_pressure')->nullable();
                $table->string('contact_person')->nullable();
                $table->date('date_of_birth')->nullable();
                $table->string('phone_number')->nullable();
                $table->string('email')->nullable();
                $table->string('gender', 10)->nullable()->default('Male');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
                $table->boolean('archived')->default(false);
                $table->timestamps();
                $table->string('phone', 20)->nullable();
                $table->string('emergency_contact')->nullable();
                $table->string('emergency_phone', 20)->nullable();
                $table->text('medical_history')->nullable();
                $table->text('allergies')->nullable();
                $table->text('notes')->nullable();
                $table->string('status', 20)->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->string('emergency_contact_name')->nullable();
                $table->string('emergency_contact_relationship', 100)->nullable();
                $table->string('emergency_contact_phone', 20)->nullable();
                $table->text('emergency_contact_address')->nullable();
                $table->decimal('height')->nullable();
                $table->decimal('weight')->nullable();
                
                // Indexes
                $table->index('archived', 'patients_archived_index');
                $table->index('email', 'patients_email_index');
                $table->index('patient_name', 'patients_patient_name_index');
            });
        }
        
        // Appointments Table
        if (!Schema::hasTable('appointments')) {
            Schema::create('appointments', function (Blueprint $table) {
                $table->id('appointment_id');
                $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
                $table->date('appointment_date');
                $table->time('appointment_time');
                $table->text('reason')->nullable();
                $table->string('status', 20)->default('active');
                $table->string('approval_status', 20)->default('approved');
                $table->string('reschedule_status', 20)->default('none');
                $table->date('requested_date')->nullable();
                $table->time('requested_time')->nullable();
                $table->text('reschedule_reason')->nullable();
                $table->timestamps();
                $table->text('diagnosis')->nullable();
                $table->text('treatment')->nullable();
                $table->jsonb('vital_signs')->nullable();
                $table->date('follow_up_date')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamp('cancelled_at')->nullable();
                $table->unsignedBigInteger('cancelled_by')->nullable();
                $table->text('notes')->nullable();
                
                // Indexes
                $table->index('appointment_date', 'appointments_appointment_date_index');
                $table->index('approval_status', 'appointments_approval_status_index');
                $table->index(['patient_id', 'appointment_date'], 'appointments_patient_id_appointment_date_index');
                $table->index('status', 'appointments_status_index');
                
                // Foreign keys
                $table->foreign('cancelled_by', 'appointments_cancelled_by_foreign')
                      ->references('id')->on('users')->onDelete('set null');
            });
        }
        
        // Medicines Table
        if (!Schema::hasTable('medicines')) {
            Schema::create('medicines', function (Blueprint $table) {
                $table->id();
                $table->string('medicine_name')->unique();
                $table->text('description')->nullable();
                $table->timestamps();
                $table->integer('stock_quantity')->default(0);
                $table->string('status', 20)->default('active');
                $table->integer('minimum_stock')->default(10);
                $table->integer('maximum_stock')->default(1000);
                $table->string('unit', 50)->default('pieces');
                $table->string('category', 100)->default('General');
                $table->string('dosage_form', 50)->default('Tablet');
                $table->string('strength', 50)->nullable();
                $table->string('manufacturer', 100)->nullable();
                $table->string('batch_number', 50)->nullable();
                $table->date('expiry_date')->nullable();
                $table->decimal('price_per_unit', 8, 2)->default(0);
                $table->string('supplier', 100)->nullable();
                $table->string('location', 100)->nullable();
                $table->text('notes')->nullable();
                $table->boolean('requires_prescription')->default(false);
                $table->text('side_effects')->nullable();
                $table->text('contraindications')->nullable();
                $table->string('generic_name')->nullable();
                $table->string('brand_name')->nullable();
                $table->string('therapeutic_class')->nullable();
                $table->text('indication')->nullable();
                $table->text('dosage_instructions')->nullable();
                $table->string('age_restrictions')->nullable();
                $table->string('unit_measure')->nullable();
                $table->integer('balance_per_card')->nullable();
                $table->integer('on_hand_per_count')->nullable();
                $table->integer('shortage_overage')->default(0);
                $table->text('inventory_remarks')->nullable();
                $table->date('manufacturing_date')->nullable();
                $table->string('storage_conditions')->nullable();
                $table->text('drug_interactions')->nullable();
                $table->string('pregnancy_category', 10)->nullable();
                $table->text('warnings')->nullable();
                $table->string('medicine_image')->nullable();
                
                // Index
                $table->index('medicine_name', 'medicines_medicine_name_index');
            });
        }
        
        // Prescriptions Table
        if (!Schema::hasTable('prescriptions')) {
            Schema::create('prescriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
                $table->unsignedBigInteger('medicine_id')->nullable();
                $table->string('medicine_name');
                $table->integer('quantity');
                $table->string('dosage');
                $table->text('instructions')->nullable();
                $table->string('status', 20)->default('active');
                $table->date('prescribed_date');
                $table->date('expiry_date')->nullable();
                $table->text('notes')->nullable();
                $table->decimal('unit_price')->nullable();
                $table->decimal('total_amount')->nullable();
                $table->timestamps();
                $table->integer('dispensed_quantity')->default(0);
                $table->date('dispensed_date')->nullable();
                $table->unsignedBigInteger('dispensed_by')->nullable();
                $table->string('prescription_number', 50)->nullable();
                $table->integer('refills_remaining')->default(0);
                $table->string('frequency', 100)->nullable();
                $table->integer('duration_days')->nullable();
                $table->unsignedBigInteger('appointment_id')->nullable();
                $table->string('generic_name')->nullable();
                $table->unsignedBigInteger('prescribed_by')->nullable();
                
                // Indexes
                $table->index('appointment_id', 'prescriptions_appointment_id_index');
                $table->index(['patient_id', 'status'], 'prescriptions_patient_id_status_index');
                $table->index('prescribed_date', 'prescriptions_prescribed_date_index');
                
                // Foreign keys
                $table->foreign('appointment_id', 'prescriptions_appointment_id_foreign')
                      ->references('appointment_id')->on('appointments')->onDelete('set null');
                $table->foreign('dispensed_by', 'prescriptions_dispensed_by_foreign')
                      ->references('id')->on('users')->onDelete('set null');
            });
        }
        
        // Patient Visits Table
        if (!Schema::hasTable('patient_visits')) {
            Schema::create('patient_visits', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
                $table->unsignedBigInteger('appointment_id')->nullable();
                $table->unsignedBigInteger('doctor_id')->nullable();
                $table->unsignedBigInteger('nurse_id')->nullable();
                $table->date('visit_date');
                $table->date('next_visit_date')->nullable();
                $table->string('bp')->nullable()->comment('Blood pressure');
                $table->decimal('temperature', 5, 2)->nullable()->comment('Temperature in Celsius');
                $table->integer('pulse_rate')->nullable()->comment('Pulse rate per minute');
                $table->integer('rr')->nullable()->comment('Respiratory rate per minute');
                $table->integer('spo2')->nullable()->comment('Oxygen saturation percentage');
                $table->decimal('height', 5, 2)->nullable()->comment('Height in cm');
                $table->decimal('weight', 5, 2)->nullable()->comment('Weight in kg');
                $table->decimal('bmi', 5, 2)->nullable()->comment('Body Mass Index');
                $table->string('disease')->nullable();
                $table->text('symptoms')->nullable();
                $table->text('diagnosis')->nullable();
                $table->text('treatment_plan')->nullable();
                $table->text('notes')->nullable();
                $table->text('doctor_observations')->nullable();
                $table->text('recommendations')->nullable();
                $table->string('visit_type')->default('Regular');
                $table->string('status')->default('In Progress');
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
                
                // Indexes
                $table->index('appointment_id', 'patient_visits_appointment_id_index');
                $table->index('doctor_id', 'patient_visits_doctor_id_index');
                $table->index('patient_id', 'patient_visits_patient_id_index');
                $table->index('status', 'patient_visits_status_index');
                $table->index('visit_date', 'patient_visits_visit_date_index');
                $table->index('visit_type', 'patient_visits_visit_type_index');
                
                // Foreign keys
                $table->foreign('appointment_id', 'patient_visits_appointment_id_foreign')
                      ->references('appointment_id')->on('appointments')->onDelete('set null');
                $table->foreign('doctor_id', 'patient_visits_doctor_id_foreign')
                      ->references('id')->on('users')->onDelete('set null');
                $table->foreign('nurse_id', 'patient_visits_nurse_id_foreign')
                      ->references('id')->on('users')->onDelete('set null');
            });
        }
        
        // Medical Notes Table
        if (!Schema::hasTable('medical_notes')) {
            Schema::create('medical_notes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
                $table->unsignedBigInteger('appointment_id')->nullable();
                $table->unsignedBigInteger('patient_visit_id')->nullable();
                $table->string('title')->nullable();
                $table->text('content');
                $table->string('note_type')->default('general');
                $table->string('priority')->default('normal');
                $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
                $table->timestamp('note_date')->useCurrent();
                $table->boolean('is_private')->default(false);
                $table->json('tags')->nullable();
                $table->timestamps();
                
                // Indexes
                $table->index('appointment_id', 'medical_notes_appointment_id_index');
                $table->index('created_by', 'medical_notes_created_by_index');
                $table->index('note_date', 'medical_notes_note_date_index');
                $table->index('note_type', 'medical_notes_note_type_index');
                $table->index('patient_id', 'medical_notes_patient_id_index');
                $table->index('patient_visit_id', 'medical_notes_patient_visit_id_index');
                $table->index('priority', 'medical_notes_priority_index');
                
                // Foreign keys
                $table->foreign('appointment_id', 'medical_notes_appointment_id_foreign')
                      ->references('appointment_id')->on('appointments')->onDelete('cascade');
                $table->foreign('patient_visit_id', 'medical_notes_patient_visit_id_foreign')
                      ->references('id')->on('patient_visits')->onDelete('cascade');
            });
        }
        
        // Conversations Table
        if (!Schema::hasTable('conversations')) {
            Schema::create('conversations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('patient_id')->nullable();
                $table->unsignedBigInteger('admin_id')->nullable();
                $table->string('subject')->nullable();
                $table->string('status', 20)->default('active');
                $table->timestamps();
                $table->boolean('is_active')->default(true);
                $table->boolean('admin_archived')->default(false);
                $table->boolean('patient_archived')->default(false);
                $table->timestamp('last_message_at')->useCurrent();
                $table->timestamp('admin_read_at')->nullable();
                $table->timestamp('patient_read_at')->nullable();
                $table->string('priority', 20)->default('normal');
                $table->string('type', 50)->default('patient_admin');
                $table->boolean('archived_by_patient')->default(false);
                $table->boolean('archived_by_admin')->default(false);
                
                // Indexes
                $table->index('last_message_at', 'idx_conversations_last_message');
                
                // Foreign keys
                $table->foreign('admin_id', 'conversations_admin_id_fkey')
                      ->references('id')->on('users')->onDelete('cascade');
                $table->foreign('patient_id', 'conversations_patient_id_fkey')
                      ->references('id')->on('patients')->onDelete('cascade');
            });
        }
        
        // Messages Table
        if (!Schema::hasTable('messages')) {
            Schema::create('messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('conversation_id')->nullable()->constrained('conversations')->onDelete('cascade');
                $table->foreignId('sender_id')->nullable()->constrained('users')->onDelete('cascade');
                $table->text('message');
                $table->boolean('is_read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
                $table->jsonb('reactions')->nullable();
                $table->boolean('has_attachment')->default(false);
                $table->string('file_path')->nullable();
                $table->string('file_name')->nullable();
                $table->string('file_type')->nullable();
                $table->string('mime_type')->nullable();
                $table->bigInteger('file_size')->nullable();
                $table->string('priority')->default('normal');
                $table->string('message_type')->default('text');
                
                // Indexes
                $table->index('conversation_id', 'messages_conversation_id_index');
                $table->index('is_read', 'messages_is_read_index');
                $table->index('sender_id', 'messages_sender_id_index');
                $table->index(['conversation_id', 'created_at'], 'idx_messages_conversation_created');
            });
        }
        
        // Settings Table
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->text('description')->nullable();
                $table->string('type')->default('string');
                $table->boolean('is_public')->default(false);
                $table->timestamps();
                
                // Indexes
                $table->index('is_public', 'settings_is_public_index');
                $table->index('key', 'settings_key_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('medical_notes');
        Schema::dropIfExists('patient_visits');
        Schema::dropIfExists('prescriptions');
        Schema::dropIfExists('medicines');
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('patients');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('users');
    }
};
