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
        Schema::table('pokemons', function (Blueprint $table) {
            $table->boolean('is_best_buddy')->default(false)->after('is_shiny');
            $table->decimal('iv_percentage', 5, 2)->after('is_best_buddy');
            $table->integer('iv_attack')->unsigned()->after('iv_percentage');
            $table->integer('iv_defense')->unsigned()->after('iv_attack');
            $table->integer('iv_stamina')->unsigned()->after('iv_defense');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pokemons', function (Blueprint $table) {
            $table->dropColumn([
                'is_best_buddy',
                'iv_percentage',
                'iv_attack',
                'iv_defense',
                'iv_stamina'
            ]);
        });
    }
};
