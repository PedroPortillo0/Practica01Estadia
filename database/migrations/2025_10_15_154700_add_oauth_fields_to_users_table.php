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
            // Agregar campos para OAuth
            $table->string('google_id')->nullable()->unique()->after('id');
            $table->string('avatar')->nullable()->after('email_verificado');
            $table->string('auth_provider')->default('local')->after('avatar'); // 'local', 'google'
            
            // Hacer el password nullable para usuarios de OAuth
            $table->string('password')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Eliminar campos de OAuth
            $table->dropColumn(['google_id', 'avatar', 'auth_provider']);
            
            // Revertir password a no nullable
            $table->string('password')->nullable(false)->change();
        });
    }
};
