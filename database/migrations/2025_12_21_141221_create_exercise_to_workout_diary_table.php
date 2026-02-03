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
                ->foreignId('exercise_id')
                ->constrained('exercises')
                ->restrictOnDelete();

            $table->enum('unit', ['minutes', 'hours', 'km', 'm'])
                ->default('minutes');

            $table->unsignedInteger('amount')->default(1);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercises_to_workout_diary');
    }
};
