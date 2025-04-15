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
        Schema::create('vendor_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('client_payment_id')->nullable()->constrained()->onDelete('set null');
            $table->date('month_year');
            $table->integer('working_days');
            $table->integer('present_days');
            $table->integer('approved_leave_days')->default(0);
            $table->decimal('daily_rate', 10, 2);
            $table->decimal('calculated_amount', 12, 2);
            $table->decimal('deductions', 12, 2)->default(0);
            $table->decimal('final_amount', 12, 2);
            $table->enum('payment_status', ['pending', 'approved', 'paid'])->default('pending');
            $table->date('payment_date')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Ensure only one payment record per vendor per month
            $table->unique(['vendor_id', 'month_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_payments');
    }
};
