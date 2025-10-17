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
        Schema::table('users', function (Blueprint $table) {
            // Primero agregar la nueva columna apellidos
            $table->string('apellidos')->nullable()->after('nombre');
        });
        
        // Migrar datos existentes
        DB::statement('UPDATE users SET apellidos = CONCAT(apellido_paterno, " ", apellido_materno)');
        
        Schema::table('users', function (Blueprint $table) {
            // Hacer la columna apellidos no nullable
            $table->string('apellidos')->nullable(false)->change();
            
            // Eliminar las columnas antiguas
            $table->dropColumn(['apellido_paterno', 'apellido_materno', 'telefono']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Restaurar columnas originales
            $table->string('apellido_paterno')->after('nombre');
            $table->string('apellido_materno')->after('apellido_paterno');
            $table->string('telefono', 10)->after('apellido_materno');
        });
        
        // Migrar datos de vuelta (esto es aproximado)
        DB::statement('UPDATE users SET apellido_paterno = SUBSTRING_INDEX(apellidos, " ", 1), apellido_materno = SUBSTRING_INDEX(apellidos, " ", -1)');
        
        Schema::table('users', function (Blueprint $table) {
            // Eliminar columna apellidos
            $table->dropColumn('apellidos');
        });
    }
};
