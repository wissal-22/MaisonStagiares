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
        Schema::create('mouvements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dossier_id')->constrained('dossiers')->cascadeOnDelete();
            $table->foreignId('internat_id')->constrained('internats')->cascadeOnDelete();
            $table->string('type'); // arrivee, depart, changement_chambre, absence_prolongee, exclusion
            $table->dateTime('date_mouvement');
            $table->foreignId('ancien_lit_id')->nullable()->constrained('lits')->nullOnDelete();
            $table->foreignId('nouveau_lit_id')->nullable()->constrained('lits')->nullOnDelete();
            $table->text('motif')->nullable();
            $table->foreignId('enregistre_par_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mouvements');
    }
};
