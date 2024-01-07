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
        Schema::create('mois', function (Blueprint $table) {
            $table->id();
            $table->string('nom_mois');
            $table->date("date_mois");
            $table->string("montant_mois");
            $table->string("nouvel_index");
            $table->string("ancien_index");
            $table->boolean("payer");
            $table->foreignId('users_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mois');
    }
};
