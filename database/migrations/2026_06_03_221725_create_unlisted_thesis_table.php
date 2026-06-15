<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unlisted_thesis', function (Blueprint $table) {
            $table->integer('UL_THESIS_ID')->autoIncrement()->primary();
            $table->integer('UL_THESIS_FINCODE')->nullable()->index();
            $table->longText('UL_THESIS_CONTENT')->nullable();
            $table->string('UL_THESIS_ACTIVE', 1)->nullable();
            $table->timestamp('UL_THESIS_INSERT_TIME')->nullable();
            $table->timestamp('UL_THESIS_UPDATE_TIME')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unlisted_thesis');
    }
};
