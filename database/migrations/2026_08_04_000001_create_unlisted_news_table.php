<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unlisted_news', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('type', 20); // text, image, video, pdf, one_pager
            $table->string('link')->nullable(); // uploaded file path or external video URL
            $table->string('video_preview_image')->nullable();
            $table->text('short_content')->nullable();
            $table->longText('content')->nullable();
            $table->string('status', 20)->default('Active')->index();
            $table->dateTime('published_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('created_by')->references('uid')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unlisted_news');
    }
};
