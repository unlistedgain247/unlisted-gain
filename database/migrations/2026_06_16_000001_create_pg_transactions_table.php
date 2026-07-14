<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pg_transactions', function (Blueprint $table) {
            $table->increments('pgt_tid');
            $table->date('pgt_transaction_date')->nullable()->index();
            $table->decimal('pgt_in_out_amount', 15, 2)->nullable();
            $table->string('pgt_transaction_type', 20)->nullable();  // 'Flow In' / 'Flow Out'
            $table->string('pgt_from_to', 50)->nullable();           // 'Customer', 'Company', 'PG', 'Bank', 'ICICI Bank', 'Insurer'
            $table->string('pgt_bank_account', 50)->nullable();       // 'Bank', 'PG', 'ICICI Bank', 'Bandhan Bank'
            $table->decimal('pgt_balance', 15, 2)->nullable();
            $table->integer('pgt_transaction_for_user_id')->nullable()->default(0)->index();
            $table->timestamp('pgt_created_on')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pg_transactions');
    }
};
