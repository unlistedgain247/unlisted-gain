<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_communication_restrictions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('user_id')->unique()->index();
            $table->tinyInteger('whatsapp')->default(1);
            $table->tinyInteger('email')->default(1);
            $table->tinyInteger('sms')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_communication_restrictions');
    }
};
