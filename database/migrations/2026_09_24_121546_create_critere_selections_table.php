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
        Schema::create('critere_selections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internat_id')->nullable()->constrained('internats')->nullOnDelete();
            $table->string('code')->unique();
            $table->string('nom');
            $table->string('type_critere'); // distance, revenu, orphelin, handicap, reconduction, merite
            $table->integer('points_base')->default(10);
            $table->decimal('poids', 5, 2)->default(1.00);
            $table->text('description')->nullable();
            $table->boolean('est_actif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('critere_selections');
    }
};
