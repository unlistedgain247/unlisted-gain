<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unlisted_stocks', function (Blueprint $table) {
            $table->dropColumn(['UL_STOCKS_INSTA_BUY_FLAG', 'UL_STOCKS_INSTA_SELL_FLAG']);
        });
    }

    public function down(): void
    {
        Schema::table('unlisted_stocks', function (Blueprint $table) {
            $table->string('UL_STOCKS_INSTA_BUY_FLAG', 3)->nullable();
            $table->string('UL_STOCKS_INSTA_SELL_FLAG', 3)->nullable();
        });
    }
};
