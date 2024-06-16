<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trades', function (Blueprint $table) {

            $table->string('tra_name', 600)->nullable()->change();

            $table->string('tra_exp_ext', 600)->nullable()->change();

            $table->string('tra_doc_recep', 600)->nullable()->change();

            $table->string('tra_matter', 2000)->change();

            //Clave foranea nula
            $table->unsignedBigInteger('tra_abo_id')->nullable()->change();

            //Año
            // $table->integer('anio');

            //Fecha de derivacion
            $table->string('tra_der_date', 255)->nullable();

            // Eliminar la clave foránea si existe
            $table->dropForeign(['tra_type_id']);

            // Eliminar el campo tra_type_id
            $table->dropColumn('tra_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trades', function (Blueprint $table) {

            $table->unsignedBigInteger('tra_type_id');
            $table->foreign('tra_type_id')->references('type_id')->on('type_references');
            $table->dropColumn('tra_der_date');
            $table->string('tra_name', 50)->nullable(false)->change();
            $table->string('tra_exp_ext', 50)->nullable()->change();
            $table->string('tra_doc_recep', 50)->nullable()->change();
            $table->string('tra_matter', 255)->nullable(false)->change();
        });
    }
};
