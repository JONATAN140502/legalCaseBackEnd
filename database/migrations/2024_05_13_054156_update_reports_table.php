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
            // Permitir que rep_tra_id sea NULL
            $table->unsignedBigInteger('rep_tra_id')->nullable()->change();
                
            // Agregar rep_exp_id como clave foránea nullable
            $table->unsignedBigInteger('rep_exp_id')->nullable();
            $table->foreign('rep_exp_id')->references('exp_id')->on('proceedings');

            // Agregar rep_ext_informe como varchar(255) nullable
            $table->string('rep_ext_informe', 255)->nullable();

            $table->string('rep_matter', 2000)->nullable();
            $table->string('rep_arrival_date', 50);

            // Agregar rep_exp_id como clave foránea nullable
            $table->unsignedBigInteger('rep_abo_id')->nullable();
            $table->foreign('rep_abo_id')->references('abo_id')->on('lawyers');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trade_reports', function (Blueprint $table) {
            // Remover la clave foránea rep_exp_id antes de eliminar la columna
            $table->dropForeign(['rep_exp_id']);
            

            $table->dropColumn('rep_exp_id');
            // Remover la columna rep_ext_informe
            $table->dropColumn('rep_ext_informe');
            $table->dropColumn('rep_matter');
            $table->dropColumn('rep_arrival_date');
            
            // Remover la clave foránea rep_tra_id antes de modificar la columna
            $table->dropForeign(['rep_tra_id']);
            $table->dropColumn('rep_tra_id');
            // Revertir rep_tra_id para que no sea nullable (asumiendo que era UNSIGNED BIGINT y NOT NULL antes)
            $table->unsignedBigInteger('rep_tra_id');
            // Volver a agregar la clave foránea de rep_tra_id si es necesario
            $table->foreign('rep_tra_id')->references('tra_id')->on('trades');
        });
        
    }
};
