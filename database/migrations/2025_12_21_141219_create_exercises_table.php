<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\FoodUnits;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->enum('unit', FoodUnits::values())
                ->default('other');

            $table->unsignedInteger('calories_per_unit')->default(0);

            $table->text('note')->nullable();

            $table->timestamps();

            $table->index(['name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};