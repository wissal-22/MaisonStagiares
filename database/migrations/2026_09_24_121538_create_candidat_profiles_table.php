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
        Schema::create('candidat_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('cin');
            $table->boolean('is_mineur')->default(false);
            $table->string('cin_tuteur')->nullable();
            $table->string('nom_tuteur')->nullable();
            $table->string('cef')->index();
            $table->date('date_naissance');
            $table->string('genre')->default('garcon'); // garcon, fille
            $table->string('ville_origine');
            $table->text('adresse')->nullable();
            $table->string('telephone')->nullable();
            $table->integer('distance_km')->default(0);
            $table->decimal('revenu_mensuel_parents', 10, 2)->default(0);
            $table->integer('nombre_freres_soeurs')->default(0);
            $table->boolean('est_orphelin')->default(false);
            $table->boolean('en_situation_handicap')->default(false);
            $table->foreignId('niveau_id')->constrained('niveaux')->cascadeOnDelete();
            $table->foreignId('filiere_id')->constrained('filieres')->cascadeOnDelete();
            $table->boolean('annee_precedente_heberge')->default(false);
            $table->string('regime_alimentaire')->nullable();
            $table->string('contact_urgence_nom')->nullable();
            $table->string('contact_urgence_telephone')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidat_profiles');
    }
};
