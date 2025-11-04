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
            // nothing to alter if table doesn't exist
            return;
        }

        // Add missing columns if needed
        Schema::table('reflections', function (Blueprint $table) {
            if (! Schema::hasColumn('reflections', 'morning_text')) {
                $table->text('morning_text')->nullable()->after('date');
            }

            if (! Schema::hasColumn('reflections', 'evening_text')) {
                $table->text('evening_text')->nullable()->after('morning_text');
            }
        });

        // Ensure unique index on (user_id, date) exists. Use information_schema check to avoid relying on DBAL.
        // Try to detect existing index by querying information_schema; fall back to creating the index if detection fails.
        $indexExists = false;
        try {
            $database = DB::connection()->getDatabaseName();
            $rows = DB::select(
                'SELECT INDEX_NAME FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND INDEX_NAME = ?',
                [$database, 'reflections', 'reflections_user_id_date_unique']
            );

            if (! empty($rows)) {
                $indexExists = true;
            }
        } catch (\Throwable $e) {
            // If detection fails, we'll attempt to create the index below and ignore any errors.
            $indexExists = false;
        }

        if (! $indexExists) {
            try {
                Schema::table('reflections', function (Blueprint $table) {
                    $table->unique(['user_id', 'date']);
                });
            } catch (\Throwable $e) {
                // ignore: index might already exist under a different name or DB doesn't allow this operation here
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

        // Drop the unique index if present (attempt, ignore errors)
        try {
            Schema::table('reflections', function (Blueprint $table) {
                $table->dropUnique('reflections_user_id_date_unique');
            });
        } catch (\Throwable $e) {
            // ignore
        }

        // Drop columns if they exist
        Schema::table('reflections', function (Blueprint $table) {
            if (Schema::hasColumn('reflections', 'morning_text')) {
                $table->dropColumn('morning_text');
            }

            if (Schema::hasColumn('reflections', 'evening_text')) {
                $table->dropColumn('evening_text');
            }
        });
    }
};
