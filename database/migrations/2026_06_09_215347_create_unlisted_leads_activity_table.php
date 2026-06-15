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
        Schema::create('unlisted_leads_activity', function (Blueprint $table) {
            $table->bigIncrements('UL_LEAD_ACTY_ID');
            $table->unsignedBigInteger('UL_LEAD_ACTY_LID')->index();
            $table->string('UL_LEAD_ACTY_UID', 100)->nullable();
            $table->string('UL_LEAD_ACTY_TYPE', 50)->nullable();
            $table->string('UL_LEAD_ACTY_DISPOSITION', 100)->nullable();
            $table->string('UL_LEAD_ACTY_SUB_DISPOSITION', 100)->nullable();
            $table->string('UL_LEAD_ACTY_REASON_FOR_CALL', 255)->nullable();
            $table->string('UL_LEAD_ACTY_PRODUCT_PITCHED', 255)->nullable();
            $table->text('UL_LEAD_ACTY_COMMENT')->nullable();
            $table->dateTime('UL_LEAD_ACTY_CALLBACK_TIME')->nullable();
            $table->dateTime('UL_LEAD_ACTY_TIMESTAMP')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unlisted_leads_activity');
    }
};
