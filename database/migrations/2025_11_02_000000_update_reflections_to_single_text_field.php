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
        if (! Schema::hasTable('reflections')) {
            return;
        }

        Schema::table('reflections', function (Blueprint $table) {
            // Agregar el campo text si no existe
            if (! Schema::hasColumn('reflections', 'text')) {
                $table->text('text')->nullable()->after('date');
            }
        });

        // Migrar datos: combinar morning_text y evening_text en text
        // Solo si existen datos en morning_text o evening_text
        if (Schema::hasColumn('reflections', 'morning_text') || Schema::hasColumn('reflections', 'evening_text')) {
            $reflections = \DB::table('reflections')
                ->whereNull('text')
                ->where(function($query) {
                    $query->whereNotNull('morning_text')
                          ->orWhereNotNull('evening_text');
                })
                ->get();

            foreach ($reflections as $reflection) {
                $parts = [];
                if (!empty($reflection->morning_text)) {
                    $parts[] = 'Mañana: ' . $reflection->morning_text;
                }
                if (!empty($reflection->evening_text)) {
                    $parts[] = 'Tarde: ' . $reflection->evening_text;
                }
                
                if (!empty($parts)) {
                    \DB::table('reflections')
                        ->where('id', $reflection->id)
                        ->update(['text' => implode("\n\n", $parts)]);
                }
            }
        }

        // Eliminar las columnas antiguas si existen
        Schema::table('reflections', function (Blueprint $table) {
            if (Schema::hasColumn('reflections', 'morning_text')) {
                $table->dropColumn('morning_text');
            }

            if (Schema::hasColumn('reflections', 'evening_text')) {
                $table->dropColumn('evening_text');
            }
        });
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

        Schema::table('reflections', function (Blueprint $table) {
            // Recrear las columnas antiguas
            if (! Schema::hasColumn('reflections', 'morning_text')) {
                $table->text('morning_text')->nullable()->after('date');
            }

            if (! Schema::hasColumn('reflections', 'evening_text')) {
                $table->text('evening_text')->nullable()->after('morning_text');
            }
        });

        // Eliminar el campo text
        Schema::table('reflections', function (Blueprint $table) {
            if (Schema::hasColumn('reflections', 'text')) {
                $table->dropColumn('text');
            }
        });
    }
};

