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
        Schema::create('campagnes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('annee_scolaire_id')->constrained('annee_scolaires')->cascadeOnDelete();
            $table->foreignId('internat_id')->nullable()->constrained('internats')->nullOnDelete();
            $table->string('titre');
            $table->dateTime('date_debut');
            $table->dateTime('date_fin');
            $table->string('statut')->default('ouverte'); // brouillon, ouverte, cloturee, deliberée, archivee
            $table->text('instructions')->nullable();
            $table->integer('quota_places')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campagnes');
    }
};
