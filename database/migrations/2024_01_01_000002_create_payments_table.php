<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Un "Payment" représente l'achat d'une amélioration in-game.
     * amount  = coût en pièces d'or dépensées
     * status  = completed | refunded
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('upgrade_id');          // clé de l'amélioration (ex: "alambic")
            $table->string('upgrade_name');        // nom lisible (ex: "Alambic de Cuivre")
            $table->bigInteger('amount');          // coût en pièces d'or
            $table->string('status')->default('completed'); // completed | refunded
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
