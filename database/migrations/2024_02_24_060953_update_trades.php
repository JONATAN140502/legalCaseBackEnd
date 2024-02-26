<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('trades', function (Blueprint $table) {
            // Modificar el tipo de dato de tra_arrival_date a varchar(255)
            $table->string('tra_arrival_date', 255)->change();
            
            $table->string('tra_name', 50)->nullable(false);
            $table->string('tra_number_ext')->nullable()->change();
            // Añadir el nuevo campo tra_doc_recep
            $table->string('tra_doc_recep', 50)->after('tra_arrival_date')->nullable();



        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trades', function (Blueprint $table) {
            // Revertir los cambios en caso necesario
            $table->date('tra_arrival_date')->change();
            $table->dropColumn('tra_doc_recep');
        });
    }
};
