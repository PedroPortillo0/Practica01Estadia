<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Renombrar la columna stoic_values a daily_challenges usando SQL directo
        // Esto evita la necesidad de Doctrine DBAL
        if (Schema::hasColumn('user_quiz_responses', 'stoic_values')) {
            DB::statement('ALTER TABLE user_quiz_responses CHANGE stoic_values daily_challenges JSON NOT NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir el cambio: daily_challenges a stoic_values
        if (Schema::hasColumn('user_quiz_responses', 'daily_challenges')) {
            DB::statement('ALTER TABLE user_quiz_responses CHANGE daily_challenges stoic_values JSON NOT NULL');
        }
    }
};
