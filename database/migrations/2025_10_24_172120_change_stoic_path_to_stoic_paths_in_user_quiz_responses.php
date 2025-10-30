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
            // Eliminar la columna stoic_path
            $table->dropColumn('stoic_path');
            
            // Agregar la nueva columna stoic_paths como JSON
            $table->json('stoic_paths')->nullable()->after('daily_challenges');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_quiz_responses', function (Blueprint $table) {
            // Eliminar la columna stoic_paths
            $table->dropColumn('stoic_paths');
            
            // Restaurar la columna stoic_path
            $table->string('stoic_path')->nullable()->after('daily_challenges');
        });
    }
};
