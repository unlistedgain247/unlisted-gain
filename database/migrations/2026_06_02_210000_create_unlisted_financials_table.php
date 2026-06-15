<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unlisted_financials', function (Blueprint $table) {
            // Composite primary key columns
            $table->integer('UL_FIN_FINCODE');
            $table->integer('UL_FIN_Period_end');
            $table->string('UL_FIN_Type', 1);
            $table->integer('UL_FIN_No_months');

            $table->integer('UL_FIN_Unit')->nullable();

            $table->double('UL_FIN_FV')->nullable();
            $table->double('UL_FIN_NUM_SHARES')->nullable();
            $table->double('UL_FIN_PROMOTERS_HOLDING')->nullable();
            $table->double('UL_FIN_ANNUAL_DIVIDEND_PER_SHARE')->nullable();
            $table->double('UL_FIN_NET_SALES')->nullable();
            $table->double('UL_FIN_OTHER_INCOME')->nullable();
            $table->double('UL_FIN_TOTAL_INCOME')->nullable();
            $table->double('UL_FIN_TOTAL_EXPENDITURE')->nullable();
            $table->double('UL_FIN_OPERATING_PROFIT')->nullable();
            $table->double('UL_FIN_INTEREST')->nullable();
            $table->double('UL_FIN_DEPRECIATION')->nullable();
            $table->double('UL_FIN_EXCEPTIONAL_INCOME')->nullable();
            $table->double('UL_FIN_PBT')->nullable();
            $table->double('UL_FIN_TAX')->nullable();
            $table->double('UL_FIN_PAT')->nullable();
            $table->double('UL_FIN_ADJUSTMENTS')->nullable();
            $table->double('UL_FIN_PROFIT_AFTER_ADJUSTMENTS')->nullable();
            $table->double('UL_FIN_ADJUSTED_EPS')->nullable();
            $table->double('UL_FIN_DPS')->nullable();
            $table->double('UL_FIN_SHAREHOLDER_FUNDS')->nullable();
            $table->double('UL_FIN_MINORITY_INTEREST')->nullable();
            $table->double('UL_FIN_BORROWINGS')->nullable();
            $table->double('UL_FIN_OTHER_NONCURRENT_LIABILITIES')->nullable();
            $table->double('UL_FIN_TOTAL_CURRENT_LIABILITIES')->nullable();
            $table->double('UL_FIN_TOTAL_LIABILITIES')->nullable();
            $table->double('UL_FIN_FIXED_ASSETS')->nullable();
            $table->double('UL_FIN_OTHER_NONCURRENT_ASSETS')->nullable();
            $table->double('UL_FIN_TOTAL_CURRENT_ASSETS')->nullable();
            $table->double('UL_FIN_TOTAL_ASSETS')->nullable();
            $table->double('UL_FIN_TOTAL_DEBT')->nullable();
            $table->double('UL_FIN_OPENING_CASH')->nullable();
            $table->double('UL_FIN_CFO')->nullable();
            $table->double('UL_FIN_CFI')->nullable();
            $table->double('UL_FIN_CFF')->nullable();
            $table->double('UL_FIN_NET_CASH_FLOW')->nullable();
            $table->double('UL_FIN_CLOSING_CASH')->nullable();
            $table->double('UL_FIN_CURRENT_LIABILITIES')->nullable();
            $table->double('UL_FIN_NON_CURRENT_LIABILITIES')->nullable();
            $table->double('UL_FIN_CURRENT_ASSETS')->nullable();
            $table->double('UL_FIN_NON_CURRENT_ASSETS')->nullable();
            $table->double('UL_FIN_CASH_FLOW_FROM_OPERATING_ACTIVITIES')->nullable();
            $table->double('UL_FIN_CASH_FLOW_FORM_INVESTING_ACTIVITIES')->nullable();
            $table->double('UL_FIN_CASH_FLOW_FROM_FINANCING_ACTIVITIES')->nullable();
            $table->double('UL_FIN_FREE_CASH_FLOW')->nullable();

            $table->string('UL_FIN_STATUS', 15)->nullable();
            $table->string('UL_FIN_INSERT_BY', 100)->nullable();
            $table->timestamp('UL_FIN_INSERT_TIME')->nullable();
            $table->timestamp('UL_FIN_UPDATE_TIME')->nullable();

            $table->primary(['UL_FIN_FINCODE', 'UL_FIN_Period_end', 'UL_FIN_Type', 'UL_FIN_No_months']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unlisted_financials');
    }
};
