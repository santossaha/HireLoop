<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('requirements', function (Blueprint $table) {
            if (!Schema::hasColumn('requirements', 'title')) {
                $table->string('title')->after('company_id');
            }
        });

        Schema::create('requirement_key_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requirement_id')->constrained()->onDelete('cascade');
            $table->foreignId('key_skill_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('requirements', function (Blueprint $table) {
            $table->dropColumn('title');
        });
        Schema::dropIfExists('requirement_key_skills');
    }
}; 