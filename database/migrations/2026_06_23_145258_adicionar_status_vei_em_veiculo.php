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
        Schema::table('veiculo', function (Blueprint $table) {
            if (!Schema::hasColumn('veiculo', 'StatusVei')) {
                $table->boolean('StatusVei')->default(true)->after('AnoVei');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('veiculo', function (Blueprint $table) {
            $table->dropColumn('StatusVei');
        });
    }
};
