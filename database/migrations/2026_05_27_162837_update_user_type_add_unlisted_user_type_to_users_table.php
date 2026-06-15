<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('user_type', 50)->default('member')->change();
            $table->enum('unlisted_user_type', ['unlisted', 'channel_partner'])->after('user_type');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('unlisted_user_type');
            $table->enum('user_type', ['unlisted', 'channel_partner'])->change();
        });
    }
};
