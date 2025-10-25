<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->string('name')->unique();
            $table->string('code')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            
            $table->index(['department_id', 'active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
