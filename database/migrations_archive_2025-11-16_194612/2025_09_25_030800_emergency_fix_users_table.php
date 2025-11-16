<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add missing columns to users table if they don't exist
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('patient')->after('password');
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status')->default('active')->after('role');
            }
            if (!Schema::hasColumn('users', 'registration_status')) {
                $table->enum('registration_status', ['pending', 'approved', 'rejected'])
                      ->default('approved')
                      ->after('status');
            }
            if (!Schema::hasColumn('users', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('registration_status');
            }
            if (!Schema::hasColumn('users', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at');
            }
            if (!Schema::hasColumn('users', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('users', 'registration_source')) {
                $table->enum('registration_source', ['admin', 'self', 'import'])
                      ->default('admin')
                      ->after('rejection_reason');
            }
            if (!Schema::hasColumn('users', 'display_name')) {
                $table->string('display_name')->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'profile_picture')) {
                $table->string('profile_picture')->nullable()->after('display_name');
            }
        });

        // Add foreign key constraint if it doesn't exist
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            });
        } catch (\Exception $e) {
            // Foreign key might already exist, ignore error
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove foreign key first
            $table->dropForeign(['approved_by']);
            
            // Drop columns that were added
            $table->dropColumn([
                'role',
                'status', 
                'registration_status',
                'approved_at',
                'approved_by',
                'rejection_reason',
                'registration_source',
                'display_name',
                'profile_picture'
            ]);
        });
    }
};