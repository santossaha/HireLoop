<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('onboardings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requirement_id')->constrained('requirements')->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->foreignId('candidate_id')->constrained('candidate_sourcings')->onDelete('cascade');
            $table->decimal('client_budget', 10, 2);
            $table->decimal('final_budget', 10, 2);
            $table->string('timesheet_link')->nullable();
            $table->string('delivery_manager_name');
            $table->date('start_date');
            $table->string('billing_term');
            $table->date('cycle_date');
            $table->enum('project_type', ['hourly', 'monthly']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('onboardings');
    }
}; 