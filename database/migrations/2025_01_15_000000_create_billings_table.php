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
        Schema::create('billings', function (Blueprint $table) {
            $table->id();
            
            // Store onboarding data directly
            $table->unsignedBigInteger('onboarding_id')->nullable();
            $table->decimal('final_budget', 20, 2);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            
            // Store requirement data directly
            $table->string('requirement_id', 50);
            $table->string('requirement_title')->nullable();
            
            // Store vendor data directly
            $table->string('vendor_name');
            $table->string('vendor_email')->nullable();
            $table->string('vendor_phone')->nullable();
            
            // Store candidate data directly
            $table->string('candidate_name');
            $table->string('candidate_email')->nullable();
            $table->string('candidate_phone')->nullable();
            
            // Billing period
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            
            // Calculation data
            $table->unsignedInteger('total_working_days');
            $table->decimal('total_leave_days', 3, 1)->default(0);
            $table->decimal('net_working_days', 3, 1);
            $table->decimal('per_day_salary', 10, 2);
            $table->decimal('monthly_salary', 12, 2);
            
            // Status and workflow
            $table->enum('status', ['pending', 'approved', 'paid', 'rejected'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->string('approved_by_name')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('paid_by_name')->nullable();
            $table->text('remarks')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['month', 'year']);
            $table->index(['onboarding_id', 'month', 'year']);
            $table->index('status');
            $table->index('requirement_id');
            $table->index('vendor_name');
            $table->index('candidate_name');

            $table->decimal('monthly_salary_without_gst', 20, 2)->nullable();
            $table->unsignedTinyInteger('gst_percentage')->default(18);
            $table->decimal('gst_amount', 12, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billings');
    }
}; 