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
        Schema::create('lits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chambre_id')->constrained('chambres')->cascadeOnDelete();
            $table->string('code');
            $table->string('position')->default('bas'); // bas, haut
            $table->string('statut')->default('libre'); // libre, occupe, maintenance
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lits');
    }
};
