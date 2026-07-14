<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demat_transactions', function (Blueprint $table) {
            $table->increments('DEMAT_TRANS_ID');
            $table->integer('DEMAT_USER_ID')->nullable()->index();
            $table->integer('DEMAT_FINCODE')->nullable()->index();
            $table->date('DEMAT_DATE')->nullable()->index();
            $table->string('DEMAT_IN_OUT_FLAG', 20)->nullable(); // 'Flow In' / 'Flow Out'
            $table->decimal('DEMAT_QTY', 12, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demat_transactions');
    }
};
