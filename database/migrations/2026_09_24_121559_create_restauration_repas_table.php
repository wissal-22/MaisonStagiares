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
        Schema::create('restauration_repas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internat_id')->constrained('internats')->cascadeOnDelete();
            $table->date('date_repas');
            $table->string('type_repas'); // petit_dejeuner, dejeuner, diner
            $table->integer('effectif_prevu')->default(0);
            $table->integer('effectif_reel')->default(0);
            $table->integer('regimes_speciaux_count')->default(0);
            $table->decimal('cout_unitaire_dh', 8, 2)->default(15.00);
            $table->decimal('cout_total_dh', 10, 2)->default(0);
            $table->text('menu_description')->nullable();
            $table->string('bon_commande_ref')->nullable();
            $table->string('statut')->default('planifie'); // planifie, servi, cloture
            $table->foreignId('enregistre_par_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['internat_id', 'date_repas', 'type_repas']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restauration_repas');
    }
};
