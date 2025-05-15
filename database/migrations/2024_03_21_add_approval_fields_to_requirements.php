<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('requirements', function (Blueprint $table) {
            if (!Schema::hasColumn('requirements', 'needs_hod_approval')) {
                $table->boolean('needs_hod_approval')->default(false);
            }
            if (!Schema::hasColumn('requirements', 'is_approved')) {
                $table->boolean('is_approved')->default(false);
            }
            if (!Schema::hasColumn('requirements', 'custom_percentage')) {
                $table->integer('custom_percentage')->nullable();
            }
            if (!Schema::hasColumn('requirements', 'show_budget_to_vendor')) {
                $table->boolean('show_budget_to_vendor')->default(false);
            }
            if (!Schema::hasColumn('requirements', 'final_budget')) {
                $table->decimal('final_budget', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('requirements', 'client_budget')) {
                $table->decimal('client_budget', 10, 2)->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('requirements', function (Blueprint $table) {
            $table->dropColumn([
                'needs_hod_approval',
                'is_approved',
                'custom_percentage',
                'show_budget_to_vendor',
                'final_budget',
                'client_budget'
            ]);
        });
    }
}; 