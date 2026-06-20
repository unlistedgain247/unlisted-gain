<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unlisted_financials', function (Blueprint $table) {
            $table->dropColumn(['UL_FIN_PROMOTERS_HOLDING', 'UL_FIN_ANNUAL_DIVIDEND_PER_SHARE']);
        });
    }

    public function down(): void
    {
        Schema::table('unlisted_financials', function (Blueprint $table) {
            $table->decimal('UL_FIN_PROMOTERS_HOLDING', 15, 2)->nullable();
            $table->decimal('UL_FIN_ANNUAL_DIVIDEND_PER_SHARE', 15, 2)->nullable();
        });
    }
};
