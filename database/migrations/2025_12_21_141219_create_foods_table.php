<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('foods', function (Blueprint $table) {
            $table->id();
            $table->string('barcode')->unique()->nullable();
            $table->string('brand')->nullable();
            $table->string('name');
            $table->string('name_hu')->nullable();

            $table->decimal('calorie', 8, 2)->default(0);
            $table->decimal('protein', 8, 2)->default(0);
            $table->decimal('fat', 8, 2)->default(0);
            $table->decimal('carb', 8, 2)->default(0);

            $table->string('unit')->default('g');
            $table->decimal('amount', 8, 2)->default(100);

            $table->text('image')->nullable();

            $table->timestamps();

            $table->index(['name', 'name_hu', 'brand']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('foods');
    }
};
