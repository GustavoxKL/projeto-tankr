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
        Schema::table('estacaoabastecimento', function (Blueprint $table) {
            // Alterar Token de INT para VARCHAR
            $table->string('Token', 20)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estacaoabastecimento', function (Blueprint $table) {
            $table->integer('Token')->change();
        });
    }
};
