<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unlisted_price_data', function (Blueprint $table) {
            $table->dropColumn('UL_PD_ASK_PRICE');
        });
    }

    public function down(): void
    {
        Schema::table('unlisted_price_data', function (Blueprint $table) {
            $table->double('UL_PD_ASK_PRICE')->nullable()->after('UL_PD_BID_PRICE');
        });
    }
};
