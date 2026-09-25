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
        Schema::create('dossiers', function (Blueprint $table) {
            $table->id();
            $table->string('numero_dossier')->unique();
            $table->foreignId('candidat_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('campagne_id')->constrained('campagnes')->cascadeOnDelete();
            $table->foreignId('internat_id')->constrained('internats')->cascadeOnDelete();
            $table->dateTime('date_depot');
            $table->string('statut')->default('soumis'); // recu, soumis, complet, incomplet, en_commission, admis, liste_attente, refuse, desiste, exclu
            $table->decimal('score_total', 8, 2)->default(0);
            $table->json('details_score')->nullable();
            $table->text('motif_decision')->nullable();
            $table->boolean('is_valide')->default(false);
            $table->boolean('valide_par_admin')->default(false); // is_validated_by_admin (Directeur / Commission)
            $table->foreignId('valide_par_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('date_validation')->nullable();
            $table->foreignId('lit_id')->nullable()->constrained('lits')->nullOnDelete();
            $table->dateTime('date_arrivee_effective')->nullable();
            $table->dateTime('date_depart_effective')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dossiers');
    }
};
