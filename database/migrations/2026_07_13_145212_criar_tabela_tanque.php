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
        Schema::create('tanque', function (Blueprint $table) {
            $table->integer('ID_TANQUE', true);

            $table->string('NomeTanque', 50);
            $table->enum('TipoCombustivelTanque', [
                'Diesel S10',
                'Diesel S500',
                'Gasolina Comum',
                'Gasolina Aditivada',
                'Etanol',
                'GNV'
            ]);
            $table->decimal('CapacidadeMaxTanque', 10, 2);      // Ex: 50000.00
            $table->decimal('QuantidadeAtualTanque', 10, 2)->default(0);
            $table->dateTime('DataUltAbastecimentoTanque')->nullable();
            $table->boolean('StatusTanque')->default(true);
            $table->date('DataCadastroTanque')->nullable();
            
            // FK Empresa
            $table->integer('FK_EMPRESA_ID_EMPRESA');
            $table->foreign('FK_EMPRESA_ID_EMPRESA')
                  ->references('ID_EMPRESA')
                  ->on('empresa')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tanque');
    }
};
