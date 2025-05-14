<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('requirements', function (Blueprint $table) {
            $table->boolean('needs_hod_approval')->default(false);
            $table->boolean('is_approved')->default(false);
            $table->decimal('custom_percentage', 5, 2)->nullable();
            $table->boolean('show_budget_to_vendor')->default(false);
            $table->decimal('final_budget', 15, 2)->nullable();
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
                'final_budget'
            ]);
        });
    }
}; 