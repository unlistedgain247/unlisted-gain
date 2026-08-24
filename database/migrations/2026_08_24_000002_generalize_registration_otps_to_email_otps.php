<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('registration_otps', 'email_otps');

        Schema::table('email_otps', function (Blueprint $table) {
            $table->string('purpose', 20)->default('registration')->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('email_otps', function (Blueprint $table) {
            $table->dropColumn('purpose');
        });

        Schema::rename('email_otps', 'registration_otps');
    }
};
