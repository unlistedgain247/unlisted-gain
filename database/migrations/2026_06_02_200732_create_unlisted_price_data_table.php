<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unlisted_price_data', function (Blueprint $table) {
            $table->integer('UL_PD_FINCODE');
            $table->timestamp('UL_PD_DATE')->useCurrent();
            $table->double('UL_PD_BID_PRICE')->nullable();
            $table->double('UL_PD_ASK_PRICE')->nullable();
            $table->tinyInteger('UL_PD_INVALID_FLAG')->nullable()->default(0);
            $table->timestamp('UL_PD_UPDTIME')->nullable();

            // Composite primary key — same as source table
            $table->primary(['UL_PD_FINCODE', 'UL_PD_DATE']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unlisted_price_data');
    }
};
