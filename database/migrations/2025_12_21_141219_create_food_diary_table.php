<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('food_diary', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->date('date');

            $table->enum('meal_type', ['breakfast', 'lunch', 'dinner', 'snack', 'other'])
                ->default('other');

            $table->unsignedInteger('calories')->default(0);
            $table->unsignedInteger('fat_g')->default(0);
            $table->unsignedInteger('carbs_g')->default(0);
            $table->unsignedInteger('protein_g')->default(0);

            $table->timestamps();

            $table->index(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_logs');
    }
};
