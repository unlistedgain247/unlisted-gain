<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pg_transactions', function (Blueprint $table) {
            $table->renameColumn('pgt_px_account', 'pgt_bank_account');
        });
    }

    public function down(): void
    {
        Schema::table('pg_transactions', function (Blueprint $table) {
            $table->renameColumn('pgt_bank_account', 'pgt_px_account');
        });
    }
};
