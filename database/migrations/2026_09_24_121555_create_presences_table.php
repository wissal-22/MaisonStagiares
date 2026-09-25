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
        Schema::create('presences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internat_id')->constrained('internats')->cascadeOnDelete();
            $table->foreignId('dossier_id')->constrained('dossiers')->cascadeOnDelete();
            $table->date('date_presence');
            $table->string('type_controle')->default('soir'); // matin, soir
            $table->string('statut')->default('present'); // present, absent_justifie, absent_injustifie, permission
            $table->text('remarque')->nullable();
            $table->foreignId('pointe_par_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['dossier_id', 'date_presence', 'type_controle']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presences');
    }
};
