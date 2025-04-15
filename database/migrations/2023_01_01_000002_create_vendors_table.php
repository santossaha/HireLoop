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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->enum('vendor_type', ['company', 'freelancer']);
            $table->string('company_name');
            $table->string('contact_person');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('skype_id')->nullable();
            $table->string('slack_id')->nullable();
            $table->foreignId('internal_poc_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('budget_3_years', 10, 2)->default(0);
            $table->decimal('budget_5_years', 10, 2)->default(0);
            $table->decimal('budget_7_years', 10, 2)->default(0);
            $table->decimal('budget_10_years', 10, 2)->default(0);
            $table->string('status')->default('active');
            $table->enum('communication_rating', ['excellent', 'good', 'average', 'bad'])->nullable();
            $table->enum('technical_rating', ['excellent', 'good', 'average', 'bad'])->nullable();
            $table->boolean('client_ready')->default(false);
            $table->string('availability')->nullable();
            $table->string('mt_ead_status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
