<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pg_transactions', function (Blueprint $table) {
            $table->string('pgt_ref_no', 100)->nullable()->after('pgt_bank_account');
            $table->string('pgt_remarks', 255)->nullable()->after('pgt_ref_no');
        });
    }

    public function down(): void
    {
        Schema::table('pg_transactions', function (Blueprint $table) {
            $table->dropColumn(['pgt_ref_no', 'pgt_remarks']);
        });
    }
};
