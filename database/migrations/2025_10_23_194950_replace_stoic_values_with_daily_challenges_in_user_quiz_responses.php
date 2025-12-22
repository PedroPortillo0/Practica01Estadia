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
        Schema::table('user_quiz_responses', function (Blueprint $table) {
            // Renombrar la columna stoic_values a daily_challenges
            $table->renameColumn('stoic_values', 'daily_challenges');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_quiz_responses', function (Blueprint $table) {
            // Revertir el cambio: daily_challenges a stoic_values
            $table->renameColumn('daily_challenges', 'stoic_values');
        });
    }
};
