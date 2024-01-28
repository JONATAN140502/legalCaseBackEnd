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
        Schema::create('proceedings', function (Blueprint $table) {
            $table->id('exp_id');
            $table->string('exp_numero', 255)->unique();
            $table->string('exp_fecha_inicio', 255)->nullable();
            $table->unsignedBigInteger('exp_pretencion')->nullable();
            $table->unsignedBigInteger('exp_materia')->nullable();
            $table->unsignedBigInteger('exp_dis_judicial')->nullable();
            $table->unsignedBigInteger('exp_instancia')->nullable();
            $table->unsignedBigInteger('exp_especialidad')->nullable();
            $table->decimal('exp_monto_pretencion', 20, 2)->nullable();
            $table->string('exp_estado_proceso', 255)->nullable();
            $table->string('multiple')->nullable()->comment('0  o 1');
            $table->unsignedBigInteger('exp_juzgado')->nullable();
            $table->unsignedBigInteger('abo_id')->nullable();
            $table->foreign('exp_juzgado')
                ->references('co_id')
                ->on('courts')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('exp_pretencion')
                ->references('pre_id')
                ->on('claims')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('exp_materia')
                ->references('mat_id')
                ->on('subjects')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('exp_dis_judicial')
                ->references('judis_id')
                ->on('judicial_districts')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('exp_instancia')
                ->references('ins_id')
                ->on('instances')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('exp_especialidad')
                ->references('esp_id')
                ->on('specialties')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('abo_id')
                ->references('abo_id')
                ->on('lawyers')
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
        Schema::dropIfExists('proceedings');
    }
};
