<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('author_bio')->nullable()->after('privilege');
            $table->string('author_linkedin')->nullable()->after('author_bio');
            $table->string('author_twitter')->nullable()->after('author_linkedin');
            $table->string('author_facebook')->nullable()->after('author_twitter');
            $table->string('author_instagram')->nullable()->after('author_facebook');
            $table->string('author_website')->nullable()->after('author_instagram');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'author_bio',
                'author_linkedin',
                'author_twitter',
                'author_facebook',
                'author_instagram',
                'author_website',
            ]);
        });
    }
};
