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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable(); // Optional conversation title
            $table->string('type')->default('patient_admin'); // patient_admin, group, etc.
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade'); // Patient user
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null'); // Admin user
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
            
            $table->index(['patient_id', 'is_active']);
            $table->index(['admin_id', 'is_active']);
            $table->index('last_message_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
