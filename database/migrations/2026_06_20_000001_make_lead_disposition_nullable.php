<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unlisted_leads', function (Blueprint $table) {
            $table->string('UL_LEAD_DISPOSITION', 50)->nullable()->change();
            $table->string('UL_LEAD_SUB_DISPOSITION', 50)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('unlisted_leads', function (Blueprint $table) {
            $table->string('UL_LEAD_DISPOSITION', 50)->nullable(false)->change();
            $table->string('UL_LEAD_SUB_DISPOSITION', 50)->nullable(false)->change();
        });
    }
};
