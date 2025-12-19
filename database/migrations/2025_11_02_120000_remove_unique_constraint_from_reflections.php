<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (! Schema::hasTable('reflections')) {
            return;
        }

        // Eliminar la restricción única de (user_id, date) para permitir múltiples reflexiones por día
        try {
            $database = DB::connection()->getDatabaseName();
            
            // Buscar el nombre exacto del índice único
            $indexes = DB::select(
                "SELECT CONSTRAINT_NAME 
                FROM information_schema.TABLE_CONSTRAINTS 
                WHERE TABLE_SCHEMA = ? 
                AND TABLE_NAME = 'reflections' 
                AND CONSTRAINT_TYPE = 'UNIQUE'",
                [$database]
            );

            foreach ($indexes as $index) {
                $constraintName = $index->CONSTRAINT_NAME;
                
                // Verificar si el índice contiene user_id y date
                $columns = DB::select(
                    "SELECT COLUMN_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = ? 
                    AND TABLE_NAME = 'reflections' 
                    AND CONSTRAINT_NAME = ?",
                    [$database, $constraintName]
                );

                $columnNames = array_map(fn($col) => $col->COLUMN_NAME, $columns);
                
                if (in_array('user_id', $columnNames) && in_array('date', $columnNames)) {
                    Schema::table('reflections', function (Blueprint $table) use ($constraintName) {
                        $table->dropUnique($constraintName);
                    });
                    break;
                }
            }
        } catch (\Throwable $e) {
            // Si falla la detección automática, intentar eliminar los nombres comunes
            $commonNames = [
                'reflections_user_id_date_unique',
                'reflections_user_id_date_unique_index',
                'user_id_date_unique'
            ];

            foreach ($commonNames as $name) {
                try {
                    Schema::table('reflections', function (Blueprint $table) use ($name) {
                        $table->dropUnique($name);
                    });
                    break;
                } catch (\Throwable $e) {
                    // Continuar con el siguiente nombre
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        if (! Schema::hasTable('reflections')) {
            return;
        }

        // Recrear la restricción única si se revierte la migración
        try {
            Schema::table('reflections', function (Blueprint $table) {
                $table->unique(['user_id', 'date'], 'reflections_user_id_date_unique');
            });
        } catch (\Throwable $e) {
            // Ignorar si ya existe
        }
    }
};

