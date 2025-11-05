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
            // Drop columns if they exist
            if (Schema::hasColumn('reflections', 'morning_reflection')) {
                $table->dropColumn('morning_reflection');
            }

            if (Schema::hasColumn('reflections', 'evening_reflection')) {
                $table->dropColumn('evening_reflection');
            }

            if (Schema::hasColumn('reflections', 'stoic_principles')) {
                $table->dropColumn('stoic_principles');
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
            // Recreate the columns if they are missing (nullable text)
            if (! Schema::hasColumn('reflections', 'morning_reflection')) {
                $table->text('morning_reflection')->nullable()->after('evening_text');
            }

            if (! Schema::hasColumn('reflections', 'evening_reflection')) {
                $table->text('evening_reflection')->nullable()->after('morning_reflection');
            }

            if (! Schema::hasColumn('reflections', 'stoic_principles')) {
                $table->text('stoic_principles')->nullable()->after('evening_reflection');
            }
        });
    }
};
