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
        Schema::create('offices_proceeding', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('expediente_id');
            $table->string('n_correlativo');
            $table->text('asunto');
            $table->date('fecha_envio')->nullable();
            $table->string('destinatario')->nullable();
            $table->timestamps();
            $table->softDeletes();
            // Definir las claves foráneas
            $table->foreign('expediente_id')->references('exp_id')->on('proceedings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('offices_proceeding', function (Blueprint $table) {
            $table->dropColumn('fecha_envio');
            $table->dropColumn('destinatario');
        });
    }
};
