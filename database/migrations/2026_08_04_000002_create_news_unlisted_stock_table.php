<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_unlisted_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id')->constrained('unlisted_news')->cascadeOnDelete();
            $table->unsignedBigInteger('ul_stocks_fincode');
            $table->foreign('ul_stocks_fincode', 'nus_fincode_fk')
                ->references('UL_STOCKS_FINCODE')->on('unlisted_stocks')->cascadeOnDelete();

            $table->unique(['news_id', 'ul_stocks_fincode'], 'nus_news_fincode_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_unlisted_stock');
    }
};
