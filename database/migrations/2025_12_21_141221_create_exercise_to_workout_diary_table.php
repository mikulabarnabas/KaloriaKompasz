<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\WorkoutUnits;

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

            $table->enum('unit', WorkoutUnits::values())
                ->default('minutes');

            $table->unsignedInteger('amount')->default(1);
            $table->decimal('burned_calories', 8, 2)->default(0);

            $table->timestamps();

            $table->index(['workout_diary_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercises_to_workout_diary');
    }
};
