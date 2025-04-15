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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            $table->string('invoice_number');
            $table->date('invoice_date');
            $table->decimal('amount', 12, 2);
            $table->date('month_year');
            $table->string('invoice_path');
            $table->foreignId('submitted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->enum('verification_status', ['pending', 'verified', 'discrepancy'])->default('pending');
            $table->text('discrepancy_notes')->nullable();
            $table->enum('payment_status', ['pending', 'approved', 'paid'])->default('pending');
            $table->timestamps();

            // Ensure invoice number is unique per vendor
            $table->unique(['vendor_id', 'invoice_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
