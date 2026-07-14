<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdrawal_request', function (Blueprint $table) {
            $table->increments('REQUEST_ID');
            $table->integer('REQUEST_USER_ID')->nullable()->index();
            $table->integer('REQUEST_FINCODE')->nullable()->index();
            $table->decimal('REQUEST_AMOUNT', 15, 2)->nullable();
            $table->string('REQUEST_TYPE', 20)->nullable();    // 'Cash' / 'Shares'
            $table->string('REQUEST_STATUS', 50)->nullable();  // NULL / '' / 'Pending' / 'Approved' / 'Rejected'
            $table->date('REQUEST_DATE')->nullable()->index();
            $table->decimal('REQUEST_QTY', 12, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawal_request');
    }
};
