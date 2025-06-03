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
            $table->string('requirement_id')->nullable();
            $table->string('vendor_id')->nullable();
            $table->string('candidate_id')->nullable();
            $table->decimal('client_budget', 20, 2);
            $table->decimal('final_budget', 20, 2);
            $table->string('timesheet_link')->nullable();
            $table->string('delivery_manager_name');
            $table->date('start_date');
            $table->string('billing_term');
            $table->date('cycle_date');
            $table->enum('project_type', ['hourly', 'monthly']);
            $table->enum('status', ['Yet to Start', 'Running', 'Hold', 'Stopped'])->default('Yet to Start');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('onboardings');
    }
}; 