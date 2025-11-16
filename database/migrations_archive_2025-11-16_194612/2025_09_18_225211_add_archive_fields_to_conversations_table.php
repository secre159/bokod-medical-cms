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
        Schema::table('conversations', function (Blueprint $table) {
            $table->boolean('is_archived')->default(false);
            $table->timestamp('archived_at')->nullable();
            $table->unsignedBigInteger('archived_by')->nullable();
            $table->boolean('admin_archived')->default(false);
            $table->boolean('patient_archived')->default(false);
            
            $table->foreign('archived_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropForeign(['archived_by']);
            $table->dropColumn([
                'is_archived',
                'archived_at', 
                'archived_by',
                'admin_archived',
                'patient_archived'
            ]);
        });
    }
};
