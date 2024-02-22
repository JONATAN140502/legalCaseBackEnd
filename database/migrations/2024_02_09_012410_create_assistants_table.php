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
        Schema::create('assistants', function (Blueprint $table) {
            $table->id('ass_id');
            $table->string('ass_carga_laboral', 255)->nullable()->comment('cantidad de casos');
            $table->string('ass_disponibilidad', 255)->nullable()->comment('ocupado o libre');
            $table->unsignedBigInteger('per_id')->nullable();
            $table->foreign('per_id')
                ->references('per_id')
                ->on('persons')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trades', function (Blueprint $table) {
            $table->dropUnique(['tra_number']);
            $table->dropUnique(['tra_number_ext']);
            $table->dropColumn('tra_type_person');
            $table->dropColumn('tra_pdf');
            $table->bigInteger('tra_per_id');
            $table->foreign('tra_per_id')->references('id')->on('nombre_tabla_per_id');
            $table->dropForeign(['tra_ass_id']);
            $table->dropColumn('tra_ass_id');
            $table->bigInteger('tra_abo_id')->change();
        });
    }
};
