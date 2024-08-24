<?php

use App\Models\User;
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
            $table->json('metrics')->nullable(false);
        });

        $users = User::all();

        foreach ($users as $user) {
            $user->metrics = [
                'visits' => 0,
                'playlistsCreated' => 0,
                'timesSearched' => 0,
                'recommendationsCreated' => 0
            ];
            $user->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('metrics');
        });
    }
};
