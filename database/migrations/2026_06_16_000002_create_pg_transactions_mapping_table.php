<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pg_transactions_mapping', function (Blueprint $table) {
            $table->increments('pgtm_id');
            $table->string('pgtm_pid', 100)->nullable()->index();  // order/policy reference
            $table->string('pgtm_tid', 50)->nullable()->index();   // pgt_tid or special code like 'NCEMI'
            $table->string('pgtm_transaction_type', 20)->nullable(); // 'Flow In' / 'Flow Out'
            $table->decimal('pgtm_in_out_amount', 15, 2)->nullable();
            $table->timestamp('pgtm_created_on')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pg_transactions_mapping');
    }
};
