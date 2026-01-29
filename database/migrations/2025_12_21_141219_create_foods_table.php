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
        Schema::create('foods', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->enum('unit', ['g', 'dkg', 'kg', 'l', 'cl', 'dl']);
            $table->unsignedInteger('amount');

            $table->unsignedInteger('fat')->default(0);
            $table->unsignedInteger('carb')->default(0);
            $table->unsignedInteger('protein')->default(0);

            $table->unsignedInteger('calorie')->default(0);
            $table->text('note')->nullable();

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
