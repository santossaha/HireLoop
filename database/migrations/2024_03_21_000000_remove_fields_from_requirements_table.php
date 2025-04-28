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
        Schema::table('requirements', function (Blueprint $table) {
            $table->dropColumn(['cv_path', 'client_budget', 'proposed_budget']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requirements', function (Blueprint $table) {
            $table->string('cv_path')->after('job_description');
            $table->decimal('client_budget', 10, 2)->after('cv_path');
            $table->decimal('proposed_budget', 10, 2)->after('client_budget');
        });
    }
}; 