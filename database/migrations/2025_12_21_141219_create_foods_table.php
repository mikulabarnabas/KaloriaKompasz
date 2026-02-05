<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\FoodUnits;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('foods', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->enum('unit', FoodUnits::baseValues());

            $table->decimal('fat', 8, 2)->default(0);
            $table->decimal('carb', 8, 2)->default(0);
            $table->decimal('protein', 8, 2)->default(0);

            $table->unsignedInteger('calorie')->default(0);
            $table->text('notes')->nullable();

            $table->string('image_paths')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foods');
    }
};
