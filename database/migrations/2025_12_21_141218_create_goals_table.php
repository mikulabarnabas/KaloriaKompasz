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
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->enum('goal_type', ['lose', 'maintain', 'gain']);

            $table->decimal('target_weight', 6, 2)->nullable();

            $table->unsignedInteger('target_calories')->nullable();

            $table->unsignedInteger('target_fat_g')->nullable();
            $table->unsignedInteger('target_carbs_g')->nullable();
            $table->unsignedInteger('target_protein_g')->nullable();

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goals');
    }
};
