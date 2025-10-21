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
        Schema::create('daily_quotes', function (Blueprint $table) {
            $table->id();
            $table->text('quote');
            $table->string('author', 100);
            $table->string('category', 50);
            $table->integer('day_of_year')->unique()->comment('Día del año (1-366)');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_quotes');
    }
};
