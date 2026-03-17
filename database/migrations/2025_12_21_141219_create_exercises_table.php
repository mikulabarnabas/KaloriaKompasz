<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\WorkoutUnits;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->string('name_hu')->nullable();

            $table->enum('unit', WorkoutUnits::values());

            $table->float('calories_per_unit')->default(0);

            $table->text('note')->nullable();

            $table->timestamps();

            $table->index(['name', 'name_hu']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};