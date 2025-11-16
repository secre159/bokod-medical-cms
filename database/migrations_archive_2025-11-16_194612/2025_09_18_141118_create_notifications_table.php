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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // appointment_reminder, medication_reminder, lab_results, etc.
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // Additional data like appointment_id, prescription_id
            $table->timestamp('scheduled_for')->nullable(); // When to show the notification
            $table->boolean('is_read')->default(false);
            $table->boolean('is_sent')->default(false);
            $table->string('priority')->default('normal'); // low, normal, high, urgent
            $table->timestamps();
            
            $table->index(['user_id', 'is_read']);
            $table->index(['scheduled_for', 'is_sent']);
            $table->index(['type', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
