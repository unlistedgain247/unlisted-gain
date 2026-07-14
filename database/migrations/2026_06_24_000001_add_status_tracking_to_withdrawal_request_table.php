<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('withdrawal_request', function (Blueprint $table) {
            $table->string('REQUEST_STATUS_COMMENTS')->nullable()->after('REQUEST_QTY');
            $table->timestamp('REQUEST_UPDATED_DATE')->nullable()->after('REQUEST_STATUS_COMMENTS');
            $table->unsignedBigInteger('REQUEST_UPDATED_BY_ID')->nullable()->after('REQUEST_UPDATED_DATE');
        });
    }

    public function down(): void
    {
        Schema::table('withdrawal_request', function (Blueprint $table) {
            $table->dropColumn(['REQUEST_STATUS_COMMENTS', 'REQUEST_UPDATED_DATE', 'REQUEST_UPDATED_BY_ID']);
        });
    }
};
