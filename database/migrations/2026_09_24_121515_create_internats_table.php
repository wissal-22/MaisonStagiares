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
        Schema::create('internats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etablissement_id')->constrained('etablissements')->cascadeOnDelete();
            $table->string('nom');
            $table->integer('capacite_totale')->default(0);
            $table->integer('capacite_garcons')->default(0);
            $table->integer('capacite_filles')->default(0);
            $table->text('description')->nullable();
            $table->text('reglement_interieur')->nullable();
            $table->date('date_arrivee_prevue')->nullable();
            $table->string('telephone')->nullable();
            $table->string('statut')->default('actif'); // actif, inactif
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internats');
    }
};
