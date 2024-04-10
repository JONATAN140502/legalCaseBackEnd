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
        Schema::table('trade_reports', function (Blueprint $table) {
            // Eliminar el campo 'rep_asunto'
            $table->dropColumn('rep_asunto');

            // Crear el campo 'rep_informe'
            $table->string('rep_informe', 50)->unique()->nullable(false);

            // Crear el campo 'rep_oficio'
            $table->string('rep_oficio', 50)->unique()->nullable(false);

            // Derivar al area (clave foranea)
            $table->unsignedBigInteger('rep_are_id');
            $table->foreign('rep_are_id')->references('are_id')->on('areas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trade_reports', function (Blueprint $table) {
            // Revertir los cambios en el caso de un rollback
            $table->string('rep_asunto', 255);

            $table->dropColumn('rep_informe');
            $table->dropColumn('rep_oficio');

            $table->dropForeign(['rep_are_id']);
            $table->dropColumn('rep_are_id');
        });
    }
};
