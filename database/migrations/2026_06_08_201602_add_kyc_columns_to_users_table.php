<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Bank details
            $table->string('bank_account_no', 30)->nullable()->after('unlisted_user_type');
            $table->string('bank_ifsc_code', 15)->nullable()->after('bank_account_no');
            $table->string('bank_holder_name', 100)->nullable()->after('bank_ifsc_code');
            $table->string('bank_name', 100)->nullable()->after('bank_holder_name');
            $table->string('bank_cancelled_check')->nullable()->after('bank_name');
            $table->tinyInteger('bank_verified')->default(0)->after('bank_cancelled_check');

            // Demat details
            $table->string('demat_dp_id', 50)->nullable()->after('bank_verified');
            $table->string('demat_dp_name', 100)->nullable()->after('demat_dp_id');
            $table->string('demat_cml_copy')->nullable()->after('demat_dp_name');
            $table->tinyInteger('demat_verified')->default(0)->after('demat_cml_copy');

            // PAN details
            $table->string('user_pan_no', 10)->nullable()->after('demat_verified');
            $table->string('user_pan_image')->nullable()->after('user_pan_no');
            $table->tinyInteger('user_pan_verified')->default(0)->after('user_pan_image');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'bank_account_no', 'bank_ifsc_code', 'bank_holder_name',
                'bank_name', 'bank_cancelled_check', 'bank_verified',
                'demat_dp_id', 'demat_dp_name', 'demat_cml_copy', 'demat_verified',
                'user_pan_no', 'user_pan_image', 'user_pan_verified',
            ]);
        });
    }
};
