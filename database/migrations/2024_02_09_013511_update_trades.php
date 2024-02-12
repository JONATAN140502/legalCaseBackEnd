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
            $table->unique('tra_number');
            $table->unique('tra_number_ext');
            $table->string('tra_type_person', 50);
            $table->string('tra_pdf', 255)->nullable();

            $table->dropForeign(['tra_per_id']);
            $table->dropColumn('tra_per_id');
            
            $table->unsignedBigInteger('tra_ass_id')->nullable();
            $table->foreign('tra_ass_id')->references('ass_id')->on('assistants');

            $table->unsignedBigInteger('tra_abo_id')->nullable()->change();
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
