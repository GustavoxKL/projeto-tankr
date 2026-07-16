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
        Schema::create('tanque_estacao', function (Blueprint $table) {
            $table->integer('ID_TANQUE');
            $table->integer('ID_ESTACAO');
            
            $table->primary(['ID_TANQUE', 'ID_ESTACAO']);
            
            $table->foreign('ID_TANQUE')
                  ->references('ID_TANQUE')
                  ->on('tanque')
                  ->onDelete('cascade');
                  
            $table->foreign('ID_ESTACAO')
                  ->references('ID_ESTACAO')
                  ->on('estacaoabastecimento')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tanque_estacao');
    }
};
