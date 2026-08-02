<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unlisted_documents', function (Blueprint $table) {
            $table->integer('UL_DOC_ID')->autoIncrement()->primary();
            $table->integer('UL_DOC_FINCODE')->index();
            $table->string('UL_DOC_TYPE', 50)->index();
            $table->string('UL_DOC_RESEARCH_HOUSE', 100)->nullable();
            $table->date('UL_DOC_DATE')->nullable();
            $table->string('UL_DOC_PERIOD_MM', 2)->nullable();
            $table->string('UL_DOC_PERIOD_YY', 4)->nullable();
            $table->string('UL_DOC_DESCRIPTION', 500)->nullable();
            $table->string('UL_DOC_FILE_PATH')->nullable();
            $table->string('UL_DOC_FILELINK')->nullable();
            $table->string('UL_DOC_STATUS', 1)->default('1')->index();
            $table->string('UL_DOC_INSERT_BY')->nullable();
            $table->timestamp('UL_DOC_INSERT_TIME')->nullable();
            $table->timestamp('UL_DOC_UPDATE_TIME')->nullable();

            $table->index(['UL_DOC_FINCODE', 'UL_DOC_STATUS']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unlisted_documents');
    }
};
