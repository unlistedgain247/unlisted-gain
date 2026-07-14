<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pg_transactions', function (Blueprint $table) {
            $table->tinyInteger('pgt_direct_flag')->nullable()->default(0)->after('pgt_remarks');
            $table->tinyInteger('pgt_commission_flag')->nullable()->default(0)->after('pgt_direct_flag');
            $table->tinyInteger('pgt_TDS_flag')->nullable()->default(0)->after('pgt_commission_flag');
        });
    }

    public function down(): void
    {
        Schema::table('pg_transactions', function (Blueprint $table) {
            $table->dropColumn(['pgt_direct_flag', 'pgt_commission_flag', 'pgt_TDS_flag']);
        });
    }
};
