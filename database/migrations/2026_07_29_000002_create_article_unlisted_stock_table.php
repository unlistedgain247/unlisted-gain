<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_unlisted_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
            $table->unsignedBigInteger('ul_stocks_fincode');
            $table->foreign('ul_stocks_fincode', 'aus_fincode_fk')
                ->references('UL_STOCKS_FINCODE')->on('unlisted_stocks')->cascadeOnDelete();

            $table->unique(['article_id', 'ul_stocks_fincode'], 'aus_article_fincode_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_unlisted_stock');
    }
};
