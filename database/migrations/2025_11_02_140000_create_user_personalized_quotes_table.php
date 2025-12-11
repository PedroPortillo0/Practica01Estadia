<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('user_personalized_quotes', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->date('date');
            $table->text('personalized_quote');
            $table->text('explanation');
            $table->unsignedBigInteger('original_quote_id')->nullable();
            $table->integer('day_of_year')->comment('Día del año de la frase original');
            $table->string('original_author')->nullable();
            $table->string('original_category')->nullable();
            $table->timestamps();

            // Índice único: un usuario solo puede tener una frase personalizada por día
            $table->unique(['user_id', 'date']);
            
            // Foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('original_quote_id')->references('id')->on('daily_quotes')->onDelete('set null');
            
            // Índices para búsquedas rápidas
            $table->index('user_id');
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('user_personalized_quotes');
    }
};

