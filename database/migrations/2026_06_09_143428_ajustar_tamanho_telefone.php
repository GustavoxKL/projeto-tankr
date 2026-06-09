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
        Schema::table('motorista', function (Blueprint $table) {
            $table->string('TelefoneMot', 11)->nullable()->change();
        });

        Schema::table('empresa', function (Blueprint $table) {
            $table->string('TelefoneEmpresa', 11)->nullable()->change();
        });

        Schema::table('usuario', function (Blueprint $table) {
            $table->string('TelefoneUser', 11)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('motorista', function (Blueprint $table) {
            $table->string('TelefoneMot', 15)->nullable()->change();
        });

        Schema::table('empresa', function (Blueprint $table) {
            $table->string('TelefoneEmpresa', 15)->nullable()->change();
        });

        Schema::table('usuario', function (Blueprint $table) {
            $table->string('TelefoneUser', 15)->nullable()->change();
        });
    }
};
