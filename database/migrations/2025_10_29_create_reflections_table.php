<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reflections', function (Blueprint $table) {
            $table->id();
            // user_id as string to match users.id (UUID) used in this project
            $table->string('user_id');
            $table->date('date');
            $table->text('morning_reflection')->nullable();
            $table->text('evening_reflection')->nullable();
            $table->json('stoic_principles')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'date']);

            // Add foreign key constraint referencing users.id (string UUID)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reflections');
    }
};