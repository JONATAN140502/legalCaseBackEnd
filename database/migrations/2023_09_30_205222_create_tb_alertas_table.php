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
        Schema::create('alerts', function (Blueprint $table) {
            $table->id('ale_id');
            $table->string('ale_fecha_vencimiento', 255)->nullable();
            $table->text('ale_descripcion')->nullable();
            $table->text('ale_dias_faltantes')->nullable();
            $table->unsignedBigInteger('exp_id');
            $table->foreign('exp_id')
                ->references('exp_id')
                ->on('proceedings')
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
        Schema::dropIfExists('alerts');
    }
};
