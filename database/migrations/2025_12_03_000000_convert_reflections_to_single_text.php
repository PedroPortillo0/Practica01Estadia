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
        if (! Schema::hasTable('reflections')) {
            return;
        }

        Schema::table('reflections', function (Blueprint $table) {
            // Eliminar columnas antiguas si existen
            if (Schema::hasColumn('reflections', 'morning_text')) {
                $table->dropColumn('morning_text');
            }
            if (Schema::hasColumn('reflections', 'evening_text')) {
                $table->dropColumn('evening_text');
            }

            // Agregar columna text si no existe
            if (! Schema::hasColumn('reflections', 'text')) {
                $table->text('text')->nullable()->after('date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('reflections')) {
            return;
        }

        Schema::table('reflections', function (Blueprint $table) {
            if (Schema::hasColumn('reflections', 'text')) {
                $table->dropColumn('text');
            }

            if (! Schema::hasColumn('reflections', 'morning_text')) {
                $table->text('morning_text')->nullable()->after('date');
            }
            if (! Schema::hasColumn('reflections', 'evening_text')) {
                $table->text('evening_text')->nullable()->after('morning_text');
            }
        });
    }
};
