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
            // Eliminar las columnas de filosofía de vida
            $table->dropColumn([
                'life_purpose',
                'happiness_source', 
                'adversity_response',
                'life_development_area'
            ]);
            
            // Agregar la nueva columna de camino estoico
            $table->string('stoic_path')->nullable()->after('daily_challenges');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_quiz_responses', function (Blueprint $table) {
            // Eliminar la columna de camino estoico
            $table->dropColumn('stoic_path');
            
            // Restaurar las columnas de filosofía de vida
            $table->string('life_purpose')->nullable()->after('daily_challenges');
            $table->string('happiness_source')->nullable()->after('life_purpose');
            $table->string('adversity_response')->nullable()->after('happiness_source');
            $table->string('life_development_area')->nullable()->after('adversity_response');
        });
    }
};
