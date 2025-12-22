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
        Schema::create('verification_codes', function (Blueprint $table) {
            $table->id();
            $table->string('user_id'); // ID del usuario
            $table->string('code', 6); // Código de 6 dígitos
            $table->string('type')->default('email_verification'); // Tipo de verificación
            $table->boolean('used')->default(false); // Si ya se usó
            $table->timestamp('expires_at'); // Cuándo expira
            $table->timestamps();
            
            // Foreign key a users
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Índices para búsquedas rápidas
            $table->index(['user_id', 'code', 'used']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verification_codes');
    }
};
