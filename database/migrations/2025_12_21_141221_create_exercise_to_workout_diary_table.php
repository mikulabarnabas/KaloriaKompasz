<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exercises_to_workout_diary', function (Blueprint $table) {
            $table->id();

            $table
                ->foreignId('workout_diary_id')
                ->constrained('workout_diary')
                ->cascadeOnDelete();

            $table
                ->foreignId('exercises_id')
                ->constrained('exercises')
                ->restrictOnDelete();

            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedInteger('burned_calories')->default(0);

            $table->text('note')->nullable();

            $table->timestamps();

            $table->index(['workout_diary_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercises_to_workout_diary');
    }
};