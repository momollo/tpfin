<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_saves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->bigInteger('coins')->default(0);
            $table->bigInteger('total_coins')->default(0);
            $table->bigInteger('total_clicks')->default(0);
            $table->float('best_cps')->default(0);
            $table->json('owned_upgrades')->nullable(); // {"apprenti":2,"alambic":1,...}
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_saves');
    }
};
