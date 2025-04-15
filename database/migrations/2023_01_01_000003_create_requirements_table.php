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
        Schema::create('requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            $table->string('requirement_id')->unique();
            $table->text('job_description');
            $table->decimal('client_budget', 10, 2);
            $table->decimal('proposed_budget', 10, 2);
            $table->string('cv_path');
            $table->string('status')->default('pending');
            $table->boolean('hod_approved')->default(false);
            $table->boolean('founder_approved')->default(false);
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requirements');
    }
};
