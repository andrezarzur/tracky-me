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
            $table->text('spotify_id')->nullable();
            $table->text('spotify_access_token')->nullable();
            $table->text('spotify_refresh_token')->nullable();
            $table->timestamp('spotify_expires_in')->nullable();
            $table->text('spotify_image')->nullable();
            $table->text('spotify_product')->nullable();
            $table->integer('spotify_followers')->nullable();
            $table->boolean('password_set')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('spotify_id');
            $table->dropColumn('spotify_access_token');
            $table->dropColumn('spotify_refresh_token');
            $table->dropColumn('spotify_expires_in');
            $table->dropColumn('spotify_image');
            $table->dropColumn('spotify_product');
            $table->dropColumn('spotify_followers');
            $table->dropColumn('password_set');
        });
    }
};
