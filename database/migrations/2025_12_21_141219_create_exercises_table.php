<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->enum('unit', ['reps', 'sets', 'minutes', 'seconds', 'km', 'm', 'other'])
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