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
        Schema::create('frentista', function (Blueprint $table) {
            // ID_FRENTISTA vai ser o RFID (string)
            $table->string('ID_FRENTISTA', 50)->primary();
            $table->string('NomeFren', 100);
            $table->boolean('StatusFren')->default(true);
            $table->date('DataCadastroFren')->nullable();
            
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
        Schema::dropIfExists('frentista');
    }
};
