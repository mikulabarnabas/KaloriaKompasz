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
        Schema::create('food_to_food_diary', function (Blueprint $table) {

            $table->foreignId('food_diary_id')->constrained('food_diary')->cascadeOnDelete();
            $table->foreignId('food_id')->constrained('foods')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_to_food_diary');
    }
};
