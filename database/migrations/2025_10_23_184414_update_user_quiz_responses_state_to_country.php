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
        // Renombrar la columna state a country usando SQL directo
        // Esto evita la necesidad de Doctrine DBAL
        if (Schema::hasColumn('user_quiz_responses', 'state')) {
            DB::statement('ALTER TABLE user_quiz_responses CHANGE state country VARCHAR(255) NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir el cambio: country a state
        if (Schema::hasColumn('user_quiz_responses', 'country')) {
            DB::statement('ALTER TABLE user_quiz_responses CHANGE country state VARCHAR(255) NULL');
        }
    }
};
