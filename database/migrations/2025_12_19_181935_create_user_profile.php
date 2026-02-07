<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete()
                ->unique();

            $table->string('gender')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->unsignedSmallInteger('height_cm')->nullable();
            $table->decimal('weight_kg', 5, 2)->nullable();

            $table->enum('weight_goal', ['maintain', 'lose', 'gain'])->default('maintain');
            $table->decimal('target_weight_kg', 5, 2)->nullable();
            $table->unsignedSmallInteger('goal_period_weeks')->nullable();

            $table->decimal('calories_per_day', 6, 1)->nullable();
            $table->decimal('protein_per_day', 5, 1)->nullable();
            $table->decimal('fat_per_day', 5, 1)->nullable();
            $table->decimal('carbs_per_day', 5, 1)->nullable();
            $table->string('activity_level')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
