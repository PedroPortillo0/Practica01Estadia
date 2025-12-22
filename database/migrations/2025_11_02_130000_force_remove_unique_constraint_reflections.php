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

        // Eliminar la restricción única usando SQL directo
        // Primero intentar con el nombre estándar
        try {
            DB::statement('ALTER TABLE reflections DROP INDEX reflections_user_id_date_unique');
        } catch (\Throwable $e) {
            // Si falla, buscar el nombre real del índice
            try {
                $indexes = DB::select("SHOW INDEX FROM reflections WHERE Column_name IN ('user_id', 'date')");
                
                foreach ($indexes as $index) {
                    if ($index->Non_unique == 0) { // Es un índice único
                        DB::statement("ALTER TABLE reflections DROP INDEX `{$index->Key_name}`");
                        break;
                    }
                }
            } catch (\Throwable $e2) {
                // Si aún falla, intentar eliminar cualquier índice único que contenga user_id y date
                try {
                    $result = DB::select("
                        SELECT CONSTRAINT_NAME 
                        FROM information_schema.TABLE_CONSTRAINTS 
                        WHERE TABLE_SCHEMA = DATABASE() 
                        AND TABLE_NAME = 'reflections' 
                        AND CONSTRAINT_TYPE = 'UNIQUE'
                    ");
                    
                    foreach ($result as $constraint) {
                        $columns = DB::select("
                            SELECT COLUMN_NAME 
                            FROM information_schema.KEY_COLUMN_USAGE 
                            WHERE TABLE_SCHEMA = DATABASE() 
                            AND TABLE_NAME = 'reflections' 
                            AND CONSTRAINT_NAME = ?
                        ", [$constraint->CONSTRAINT_NAME]);
                        
                        $columnNames = array_map(fn($col) => $col->COLUMN_NAME, $columns);
                        
                        if (in_array('user_id', $columnNames) && in_array('date', $columnNames)) {
                            DB::statement("ALTER TABLE reflections DROP INDEX `{$constraint->CONSTRAINT_NAME}`");
                            break;
                        }
                    }
                } catch (\Throwable $e3) {
                    // Si todo falla, simplemente continuar
                    // El error se mostrará en la siguiente inserción si la restricción aún existe
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

        // Recrear la restricción única si se revierte
        try {
            Schema::table('reflections', function (Blueprint $table) {
                $table->unique(['user_id', 'date'], 'reflections_user_id_date_unique');
            });
        } catch (\Throwable $e) {
            // Ignorar si ya existe
        }
    }
};

