<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unlisted_stocks', function (Blueprint $table) {
            // Primary key — bigint as requested
            $table->bigIncrements('UL_STOCKS_FINCODE');

            // Company identity
            $table->string('UL_STOCKS_COMPNAME', 255)->nullable()->index();
            $table->string('UL_STOCKS_SLUG', 255)->unique();
            $table->string('UL_STOCKS_LOGO_LINK', 255)->nullable();
            $table->integer('UL_STOCKS_IND_CODE')->nullable();
            $table->string('UL_STOCKS_INDUSTRY', 100)->nullable();
            $table->string('UL_STOCKS_ISIN', 20)->nullable();
            $table->string('UL_STOCKS_S_NAME', 100)->nullable()->index();

            // Management
            $table->string('UL_STOCKS_CHAIRMAN', 50)->nullable();
            $table->string('UL_STOCKS_MDIR', 100)->nullable();

            // Classification
            $table->string('UL_STOCKS_CATEGORY', 100)->nullable();
            $table->string('UL_STOCKS_INC_MONTH', 15)->nullable();
            $table->string('UL_STOCKS_INC_YEAR', 4)->nullable();
            $table->string('UL_STOCKS_STATUS', 15)->nullable()->index();

            // Address
            $table->string('UL_STOCKS_ADD1', 500)->nullable();
            $table->string('UL_STOCKS_ADD2', 500)->nullable();
            $table->string('UL_STOCKS_ADD3', 500)->nullable();
            $table->string('UL_STOCKS_CITY_NAME', 50)->nullable();
            $table->string('UL_STOCKS_PINCODE', 20)->nullable();
            $table->string('UL_STOCKS_STATE_NAME', 50)->nullable();

            // Contact
            $table->string('UL_STOCKS_PHONE', 500)->nullable();
            $table->string('UL_STOCKS_FAX_NO', 150)->nullable();
            $table->string('UL_STOCKS_WEBSITE', 150)->nullable();
            $table->string('UL_STOCKS_E_MAIL', 150)->nullable();

            // Details
            $table->mediumText('UL_STOCKS_ABOUT')->nullable();

            // Audit
            $table->string('UL_STOCKS_INSERT_BY', 150)->nullable();
            $table->timestamp('UL_STOCKS_INSERT_TIME')->nullable();
            $table->timestamp('UL_STOCKS_UPDATE_TIME')->nullable();

            // Ratings & flags
            $table->string('UL_STOCKS_COMP_RATING', 2)->nullable();
            $table->string('UL_STOCKS_VALUATION_RATING', 2)->nullable();
            $table->string('UL_STOCKS_LOT_SIZE', 5)->nullable();
            $table->string('UL_STOCKS_INSTA_BUY_FLAG', 3)->nullable();
            $table->string('UL_STOCKS_INSTA_SELL_FLAG', 3)->nullable();
            $table->string('UL_STOCKS_ROFR_FLAG', 3)->nullable();
            $table->string('UL_STOCKS_DEMAT_ACCOUNT_REQ', 45)->nullable();
            $table->string('UL_STOCKS_BUY_SELL_FLAG', 3)->nullable()->default('Yes');

            // Type & publish
            $table->string('UL_STOCKS_COMPNAME_TYPE', 20)->index();
            $table->string('UL_STOCKS_Qtr_Data_Publish', 3)->default('Yes');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unlisted_stocks');
    }
};
