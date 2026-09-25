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
        Schema::table('candidat_profiles', function (Blueprint $table) {
            $table->string('cin')->nullable()->change();
            $table->string('cef')->nullable()->change();
            $table->date('date_naissance')->nullable()->change();
            $table->string('ville_origine')->nullable()->change();
            $table->foreignId('niveau_id')->nullable()->change();
            $table->foreignId('filiere_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidat_profiles', function (Blueprint $table) {
            $table->string('cin')->nullable(false)->change();
            $table->string('cef')->nullable(false)->change();
            $table->date('date_naissance')->nullable(false)->change();
            $table->string('ville_origine')->nullable(false)->change();
            $table->foreignId('niveau_id')->nullable(false)->change();
            $table->foreignId('filiere_id')->nullable(false)->change();
        });
    }
};
