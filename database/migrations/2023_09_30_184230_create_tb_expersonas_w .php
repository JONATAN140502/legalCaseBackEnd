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
        Schema::create('persons', function (Blueprint $table) {
            $table->id('per_id');
            //natural
            $table->string('nat_dni', 8)->unique()->nullable();
            $table->string('nat_apellido_paterno', 55)->nullable();
            $table->string('nat_apellido_materno', 55)->nullable();
            $table->string('nat_nombres', 55)->nullable();
            $table->string('nat_telefono', 55)->nullable();
            $table->string('nat_correo', 55)->unique()->nullable();
            //juridica
            $table->string('jur_ruc', 255)->unique()->nullable();
            $table->string('jur_razon_social', 255)->nullable();
            $table->string('jur_telefono', 255)->nullable();
            $table->string('jur_correo', 255)->nullable();
            $table->string('jur_rep_legal', 255)->nullable()->comment('representante legal');
            //docente -estudiente-admin -o otro-demandante o demandado
            $table->string('tipo_procesal', 55)->nullable();
            $table->string('per_condicion', 55)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persons');
    }
};
