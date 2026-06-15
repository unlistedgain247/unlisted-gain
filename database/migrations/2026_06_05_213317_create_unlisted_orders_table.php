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
        Schema::create('unlisted_orders', function (Blueprint $table) {
            $table->increments('UL_ORD_ID');
            $table->integer('UL_ORD_USER_ID')->nullable();
            $table->integer('UL_ORD_INTERMEDIARY_USER_ID')->nullable();
            $table->double('UL_ORD_INTERMEDIARY_MARGIN')->nullable();
            $table->double('UL_ORD_INTERMEDIARY_COMMISSION')->nullable();
            $table->integer('UL_ORD_FINCODE')->nullable()->index();
            $table->string('UL_ORD_TYPE', 20)->nullable();
            $table->integer('UL_ORD_QUANTITY')->nullable();
            $table->double('UL_ORD_PRICE_PER_SHARE')->nullable();
            $table->double('UL_ORD_AMOUNT')->nullable();
            $table->string('UL_ORD_STATUS', 150)->nullable();
            $table->string('UL_ORD_SUB_STATUS', 100)->nullable();
            $table->mediumText('UL_ORD_OTHER_DEAL_TERMS')->nullable();
            $table->timestamp('UL_ORD_INSERT_TIME')->nullable();
            $table->timestamp('UL_ORD_UPDATE_TIME')->nullable();
            $table->dateTime('UL_ORD_DATE')->nullable();
            $table->integer('UL_ORD_ADDED_BY')->nullable()->default(0);
            $table->double('UL_ORD_LP')->nullable();
            $table->double('UL_ORD_MLP')->nullable();
            $table->integer('UL_ORD_PAYMENT_PROOF_ID')->nullable();
            $table->tinyInteger('UL_ORD_DIRECT_FLAG')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unlisted_orders');
    }
};
