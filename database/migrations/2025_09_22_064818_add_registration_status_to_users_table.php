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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('registration_status', ['pending', 'approved', 'rejected'])
                  ->default('approved')
                  ->after('status')
                  ->comment('Status of patient self-registration');
            
            $table->timestamp('approved_at')
                  ->nullable()
                  ->after('registration_status')
                  ->comment('When admin approved the registration');
            
            $table->unsignedBigInteger('approved_by')
                  ->nullable()
                  ->after('approved_at')
                  ->comment('Admin who approved the registration');
            
            $table->text('rejection_reason')
                  ->nullable()
                  ->after('approved_by')
                  ->comment('Reason for rejecting registration');
            
            $table->enum('registration_source', ['admin', 'self', 'import'])
                  ->default('admin')
                  ->after('rejection_reason')
                  ->comment('How the user was registered');
            
            // Add foreign key for approved_by
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'registration_status',
                'approved_at',
                'approved_by',
                'rejection_reason',
                'registration_source'
            ]);
        });
    }
};
