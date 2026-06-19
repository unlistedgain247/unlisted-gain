<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fill existing NULLs before switching to NOT NULL
        \DB::table('unlisted_stocks')
            ->whereNull('UL_STOCKS_STATUS')
            ->update(['UL_STOCKS_STATUS' => '1']);

        Schema::table('unlisted_stocks', function (Blueprint $table) {
            $table->string('UL_STOCKS_STATUS', 15)->default('1')->change();
        });
    }

    public function down(): void
    {
        Schema::table('unlisted_stocks', function (Blueprint $table) {
            $table->string('UL_STOCKS_STATUS', 15)->nullable()->default(null)->change();
        });
    }
};
