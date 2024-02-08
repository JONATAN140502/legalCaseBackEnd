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
        Schema::table('trades', function (Blueprint $table) {
            // Eliminar la columna tra_format
            $table->dropColumn('tra_format');
            
            // Agregar softDeletes
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trades', function (Blueprint $table) {
            // Revertir los cambios si es necesario
            $table->char('tra_format', 1);
            
            // Eliminar softDeletes
            $table->dropSoftDeletes();
        });
    }
};
