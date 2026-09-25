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
        Schema::create('incident_reclamations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internat_id')->constrained('internats')->cascadeOnDelete();
            $table->foreignId('dossier_id')->nullable()->constrained('dossiers')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // déclarant (candidat ou gestionnaire)
            $table->string('type'); // reclamation, incident_discipline, maintenance_chambre
            $table->string('priorite')->default('moyenne'); // faible, moyenne, haute, urgente
            $table->string('titre');
            $table->text('description');
            $table->foreignId('chambre_id')->nullable()->constrained('chambres')->nullOnDelete();
            $table->string('statut')->default('ouvert'); // ouvert, en_cours, resolu, rejete
            $table->text('reponse_gestionnaire')->nullable();
            $table->foreignId('traite_par_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('date_resolution')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incident_reclamations');
    }
};
