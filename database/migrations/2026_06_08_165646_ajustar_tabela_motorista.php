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
            // Remover colunas que não vamos usar
            if (Schema::hasColumn('motorista', 'EmailMot')) {
                $table->dropColumn('EmailMot');
            }
            
            if (Schema::hasColumn('motorista', 'SenhaMot')) {
                $table->dropColumn('SenhaMot');
            }
            
            if (Schema::hasColumn('motorista', 'CPF')) {
                $table->dropColumn('CPF');
            }
            
            // Adicionar CNH (opcional)
            if (!Schema::hasColumn('motorista', 'CNHMot')) {
                $table->string('CNHMot', 11)->nullable()->after('NomeMot');
            }
            
            // Adicionar Status
            if (!Schema::hasColumn('motorista', 'StatusMot')) {
                $table->boolean('StatusMot')->default(true)->after('TelefoneMot');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('motorista', function (Blueprint $table) {
            $table->string('EmailMot', 100)->nullable();
            $table->string('SenhaMot', 255)->nullable();
            $table->string('CPF', 14)->nullable();
            $table->dropColumn(['CNHMot', 'StatusMot']);
        });
    }
};
