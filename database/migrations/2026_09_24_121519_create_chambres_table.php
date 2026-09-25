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
        Schema::create('chambres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batiment_id')->constrained('batiments')->cascadeOnDelete();
            $table->string('numero');
            $table->integer('etage')->default(0);
            $table->integer('capacite')->default(2);
            $table->string('type')->default('standard'); // standard, pmr, individuelle
            $table->string('statut')->default('disponible'); // disponible, complete, maintenance
            $table->text('equipements')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chambres');
    }
};
