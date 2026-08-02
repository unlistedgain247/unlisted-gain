<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Composite/single-column indexes for the filter patterns that show up
     * repeatedly across the admin dashboards, PG reports, and public pages —
     * none of these existed before, so most of these queries were full-table
     * scans. Purely additive (no column/type changes), safe to roll back.
     */
    public function up(): void
    {
        Schema::table('unlisted_orders', function (Blueprint $table) {
            $table->index(['UL_ORD_STATUS', 'UL_ORD_DATE'], 'ul_ord_status_date_idx');
        });

        Schema::table('unlisted_price_data', function (Blueprint $table) {
            $table->index(['UL_PD_FINCODE', 'UL_PD_INVALID_FLAG', 'UL_PD_DATE'], 'ul_pd_fincode_flag_date_idx');
        });

        Schema::table('unlisted_financials', function (Blueprint $table) {
            $table->index(['UL_FIN_FINCODE', 'UL_FIN_STATUS', 'UL_FIN_No_months', 'UL_FIN_Period_end'], 'ul_fin_fincode_status_months_idx');
        });

        Schema::table('withdrawal_request', function (Blueprint $table) {
            $table->index('REQUEST_STATUS', 'withdrawal_request_status_idx');
        });

        Schema::table('pg_transactions', function (Blueprint $table) {
            $table->index(['pgt_bank_account', 'pgt_tid'], 'pgt_bank_account_tid_idx');
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->index('published_at', 'articles_published_at_idx');
        });
    }

    public function down(): void
    {
        Schema::table('unlisted_orders', function (Blueprint $table) {
            $table->dropIndex('ul_ord_status_date_idx');
        });

        Schema::table('unlisted_price_data', function (Blueprint $table) {
            $table->dropIndex('ul_pd_fincode_flag_date_idx');
        });

        Schema::table('unlisted_financials', function (Blueprint $table) {
            $table->dropIndex('ul_fin_fincode_status_months_idx');
        });

        Schema::table('withdrawal_request', function (Blueprint $table) {
            $table->dropIndex('withdrawal_request_status_idx');
        });

        Schema::table('pg_transactions', function (Blueprint $table) {
            $table->dropIndex('pgt_bank_account_tid_idx');
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->dropIndex('articles_published_at_idx');
        });
    }
};
