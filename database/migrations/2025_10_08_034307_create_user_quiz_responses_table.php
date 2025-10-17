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
        Schema::create('user_quiz_responses', function (Blueprint $table) {
            $table->string('id')->primary(); // UUID
            $table->string('user_id');
            
            // Datos Personales
            $table->string('age_range');
            $table->string('gender');
            $table->string('sexual_orientation');
            $table->string('state')->nullable(); // Opcional
            
            // Espiritualidad
            $table->string('religious_belief');
            $table->string('spiritual_practice_level');
            $table->string('spiritual_practice_frequency');
            
            // Valores Estoicos (JSON array porque es selección múltiple)
            $table->json('stoic_values');
            
            // Filosofía de Vida
            $table->string('life_purpose');
            $table->string('happiness_source');
            $table->string('adversity_response');
            $table->string('life_development_area');
            
            $table->timestamp('completed_at');
            $table->timestamps();
            
            // Foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Índice para búsquedas rápidas
            $table->unique('user_id'); // Un usuario = un quiz
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_quiz_responses');
    }
};
