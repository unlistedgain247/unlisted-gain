<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Widens these "who/what does this row belong to" columns to unsignedBigInteger
     * (matching users.uid / unlisted_stocks.UL_STOCKS_FINCODE, both bigint unsigned)
     * and adds real foreign key constraints, restricting deletes so a user/stock
     * can't be removed out from under existing leads, orders, withdrawals, demat
     * transactions or documents.
     *
     * pg_transactions.pgt_transaction_for_user_id, unlisted_orders.UL_ORD_INTERMEDIARY_USER_ID
     * and unlisted_orders.UL_ORD_ADDED_BY are intentionally excluded here — all three use
     * 0 (not NULL) as a "not set" sentinel across several PgController reporting queries
     * (`> 0` / `<= 0` comparisons), so constraining them requires migrating that sentinel
     * to NULL and updating those queries first, as its own dedicated change.
     */
    public function up(): void
    {
        Schema::table('unlisted_leads', function (Blueprint $table) {
            $table->unsignedBigInteger('UL_LEAD_UID')->nullable()->change();
        });
        Schema::table('unlisted_leads', function (Blueprint $table) {
            $table->foreign('UL_LEAD_UID')->references('uid')->on('users')->onDelete('restrict');
        });

        Schema::table('unlisted_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('UL_ORD_USER_ID')->nullable()->change();
        });
        Schema::table('unlisted_orders', function (Blueprint $table) {
            $table->foreign('UL_ORD_USER_ID')->references('uid')->on('users')->onDelete('restrict');
        });

        Schema::table('withdrawal_request', function (Blueprint $table) {
            $table->unsignedBigInteger('REQUEST_USER_ID')->nullable()->change();
        });
        Schema::table('withdrawal_request', function (Blueprint $table) {
            $table->foreign('REQUEST_USER_ID')->references('uid')->on('users')->onDelete('restrict');
        });

        Schema::table('demat_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('DEMAT_USER_ID')->nullable()->change();
        });
        Schema::table('demat_transactions', function (Blueprint $table) {
            $table->foreign('DEMAT_USER_ID')->references('uid')->on('users')->onDelete('restrict');
        });

        Schema::table('unlisted_documents', function (Blueprint $table) {
            $table->unsignedBigInteger('UL_DOC_FINCODE')->change();
        });
        Schema::table('unlisted_documents', function (Blueprint $table) {
            $table->foreign('UL_DOC_FINCODE')->references('UL_STOCKS_FINCODE')->on('unlisted_stocks')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('unlisted_leads', function (Blueprint $table) {
            $table->dropForeign(['UL_LEAD_UID']);
        });
        Schema::table('unlisted_orders', function (Blueprint $table) {
            $table->dropForeign(['UL_ORD_USER_ID']);
        });
        Schema::table('withdrawal_request', function (Blueprint $table) {
            $table->dropForeign(['REQUEST_USER_ID']);
        });
        Schema::table('demat_transactions', function (Blueprint $table) {
            $table->dropForeign(['DEMAT_USER_ID']);
        });
        Schema::table('unlisted_documents', function (Blueprint $table) {
            $table->dropForeign(['UL_DOC_FINCODE']);
        });
    }
};
