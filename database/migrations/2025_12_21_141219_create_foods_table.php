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
            
            $table->integer('calorie')->default(0);
            $table->integer('protein')->default(0);
            $table->integer('fat')->default(0);
            $table->integer('carb')->default(0);

            $table->string('unit')->default('g');
            $table->integer('amount')->default(100);
            
            $table->text('image_path')->nullable();
            
            $table->timestamps();
            
            $table->index(['name', 'name_hu', 'brand']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('foods');
    }
};