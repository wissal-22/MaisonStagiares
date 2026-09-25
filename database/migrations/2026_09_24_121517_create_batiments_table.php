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
        Schema::create('batiments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internat_id')->constrained('internats')->cascadeOnDelete();
            $table->string('nom');
            $table->string('code')->nullable();
            $table->string('genre')->default('mixte'); // garcons, filles, mixte
            $table->integer('nombre_etages')->default(1);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batiments');
    }
};
