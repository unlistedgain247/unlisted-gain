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
        Schema::create('unlisted_leads', function (Blueprint $table) {
            $table->increments('UL_LEAD_ID');
            $table->unsignedInteger('UL_LEAD_UID')->nullable();
            $table->string('UL_LEAD_TYPE', 45)->nullable();
            $table->timestamp('UL_LEAD_INSERT_TIME')->nullable();
            $table->timestamp('UL_LEAD_UPDATE_TIME')->nullable();
            $table->timestamp('UL_LEAD_CUSTOMER_LAST_VISITED_TIME')->nullable();
            $table->string('UL_LEAD_DISPOSITION', 50)->index();
            $table->string('UL_LEAD_SUB_DISPOSITION', 50);
            $table->string('UL_LEAD_REASON_FOR_CALL', 50)->nullable();
            $table->string('UL_LEAD_PRODUCT_PITCHED', 50)->nullable();
            $table->timestamp('UL_LEAD_DISPOSITION_TIME')->nullable();
            $table->mediumText('UL_LEAD_DISPOSITION_COMMENT')->nullable();
            $table->integer('UL_LEAD_DISPOSITION_COUNT')->default(0)->nullable();
            $table->timestamp('UL_LEAD_CALLBACK_TIME')->nullable()->index();
            $table->integer('UL_LEAD_SOURCE_ID')->default(0)->index();
            $table->integer('UL_LEAD_ALLOCATED_TO')->default(0);
            $table->string('UL_LEAD_USER_TYPE', 100);
            $table->string('UL_LEAD_COMPANY', 250);
            $table->string('UL_LEAD_LANDING_PAGE', 255);
            $table->string('UL_LEAD_REQUEST_FOR_CALL', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unlisted_leads');
    }
};
