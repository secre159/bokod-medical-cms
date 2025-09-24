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
        Schema::table('messages', function (Blueprint $table) {
            // Add attachment-related fields
            $table->string('file_name')->nullable()->after('attachments');
            $table->string('file_path')->nullable()->after('file_name');
            $table->string('file_type')->nullable()->after('file_path');
            $table->integer('file_size')->nullable()->after('file_type'); // in bytes
            $table->string('mime_type')->nullable()->after('file_size');
            $table->boolean('has_attachment')->default(false)->after('mime_type');
            $table->enum('priority', ['low', 'normal', 'urgent'])->default('normal')->after('has_attachment');
            
            // Index for performance
            $table->index(['has_attachment', 'conversation_id']);
            $table->index(['priority', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn([
                'file_name',
                'file_path',
                'file_type',
                'file_size',
                'mime_type',
                'has_attachment',
                'priority'
            ]);
        });
    }
};
