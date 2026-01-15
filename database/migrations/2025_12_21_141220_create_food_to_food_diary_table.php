<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('food_to_food_diary', function (Blueprint $table) {
            $table->id();

            $table
                ->foreignId('food_diary_id')
                ->constrained('food_diary')
                ->cascadeOnDelete();

            $table
                ->foreignId('food_id')
                ->constrained('foods')
                ->restrictOnDelete();

            $table
                ->enum('meal_type', ['breakfast', 'lunch', 'dinner', 'snack', 'other'])
                ->default('other');

            $table->unsignedInteger('amount');
            $table->enum('unit', ['g', 'dkg', 'kg', 'l', 'cl', 'dl']);

            $table->timestamps();

            $table->index(['food_diary_id', 'meal_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_to_food_diary');
    }
};