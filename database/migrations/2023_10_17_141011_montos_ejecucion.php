<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('execution_amounts', function (Blueprint $table) {
            $table->id('ex_id');
            $table->decimal('ex_ejecucion_1',20,2)->nullable();
            $table->decimal('ex_ejecucion_2',20,2)->nullable();
            $table->decimal('ex_interes_1',20,2)->nullable();
            $table->decimal('ex_interes_2',20,2)->nullable();
            $table->decimal('ex_costos',20,2)->nullable();
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
        Schema::dropIfExists('execution_amounts');
    }
};
