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

        Schema::table('observations', function (Blueprint $table) {
            // Cambiar el campo 'obs_derivative' a un string de 255
            $table->string('obs_derivative', 255)->nullable()->change();

            // Eliminar el campo 'obs_abo_id'
            $table->dropForeign(['obs_abo_id']);
            $table->dropColumn('obs_abo_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('observations', function (Blueprint $table) {
            // Revertir los cambios al tamaño original del campo 'obs_derivative'
            $table->string('obs_derivative', 50)->change();

            // Revertir la eliminación del campo 'obs_abo_id'
            $table->unsignedBigInteger('obs_abo_id');
            $table->foreign('obs_abo_id')->references('abo_id')->on('lawyers');
        });
    }
};
