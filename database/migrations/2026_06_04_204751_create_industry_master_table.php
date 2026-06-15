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
        Schema::create('industry_master', function (Blueprint $table) {
            $table->integer('IM_IND_CODE')->primary();
            $table->string('IM_INDUSTRY', 100);
            $table->string('IM_FLAG', 1);
            $table->timestamp('IM_INSERT_TIME')->useCurrent();
            $table->timestamp('IM_UPDATE_TIME')->useCurrent()->useCurrentOnUpdate();
            $table->string('IM_SLUG', 255)->nullable()->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('industry_master');
    }
};
