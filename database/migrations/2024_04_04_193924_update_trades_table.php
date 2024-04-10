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
        Schema::disableForeignKeyConstraints();

        Schema::table('trades', function (Blueprint $table) {
            // Cambiar tipo de columna tra_number_ext a string de longitud 50 y renombrar a tra_exp_ext
            $table->string('tra_number_ext', 50)->nullable()->change();
            $table->renameColumn('tra_number_ext', 'tra_exp_ext');

            // Cambiar tra_doc_recep a único
            $table->unique('tra_doc_recep')->nullable()->change();

            // Eliminar columna tra_state_mp
            $table->dropColumn('tra_state_mp');
            
            // Eliminar columna tra_type_person
            $table->dropColumn('tra_type_person');


            // Cambiar tra_abo_id a no nulo
            $table->unsignedBigInteger('tra_abo_id')->nullable(false)->change();

            // Eliminar columna tra_ass_id
            $table->dropForeign(['tra_ass_id']);
            $table->dropColumn('tra_ass_id');

            // Eliminar columna tra_obs
            $table->dropColumn('tra_obs');

            // Crear columna tra_type_id como clave foránea de la tabla type_references
            $table->unsignedBigInteger('tra_type_id');
            $table->foreign('tra_type_id')->references('type_id')->on('type_references');
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('trades', function (Blueprint $table) {
            // Revertir los cambios
            $table->dropForeign(['tra_type_id']);
            $table->dropColumn('tra_type_id');

            $table->char('tra_obs', 1)->nullable();
            $table->unsignedBigInteger('tra_ass_id')->nullable();
            $table->foreign('tra_ass_id')->references('ass_id')->on('assistants');
            $table->char('tra_state_mp', 1)->nullable();
            $table->string('tra_type_person', 255);
            $table->string('tra_doc_recep', 50)->nullable()->change();
            $table->string('tra_exp_ext', 255)->change();
            $table->renameColumn('tra_exp_ext', 'tra_number_ext');
        });

        Schema::enableForeignKeyConstraints();
    }
};
