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
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            $table->foreignId('requirement_id')->constrained()->onDelete('cascade');
            $table->foreignId('interviewer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('type', ['mock', 'internal', 'client']);
            $table->timestamp('scheduled_at');
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
            $table->enum('result', ['pass', 'fail'])->nullable();
            $table->text('feedback')->nullable();
            $table->enum('communication_rating', ['excellent', 'good', 'average', 'bad'])->nullable();
            $table->enum('technical_rating', ['excellent', 'good', 'average', 'bad'])->nullable();
            $table->boolean('client_interview_ready')->nullable();
            $table->boolean('previously_worked_with_client')->nullable();
            $table->boolean('selected_in_internal')->nullable();
            $table->boolean('selected_in_client')->nullable();
            $table->decimal('last_approved_budget', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interviews');
    }
};
