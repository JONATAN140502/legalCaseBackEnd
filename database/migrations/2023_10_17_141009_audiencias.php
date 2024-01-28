<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Audiencias extends Migration
{
       /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('audiences', function (Blueprint $table) {
            $table->id('au_id');
            $table->string('au_fecha', 255)->nullable();
            $table->string('au_hora', 255)->nullable();
            $table->text('au_lugar', 255)->nullable();
            $table->text('au_link', 255)->nullable();
            $table->text('au_detalles', 255)->nullable();
            $table->text('au_dias_faltantes')->nullable();
            $table->unsignedBigInteger('exp_id')->nullable();
            $table->foreignId('per_id')->nullable();
            $table->unsignedBigInteger('abo_id')->nullable();
            $table->foreign('exp_id')
                ->references('exp_id')
                ->on('proceedings')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreign('abo_id')
                ->references('abo_id')
                ->on('lawyers')
                ->onDelete('cascade')
                ->onUpdate('cascade');
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
        Schema::dropIfExists('audiences');
    }
}
